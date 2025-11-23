<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

/**
 * Base mailable class for system emails.
 * 
 * Provides common functionality for all email messages,
 * including recipient handling, debug mode, and preview generation.
 */
class BaseMailable extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param string|array $to Recipient(s) of the email. Can be a single email, array of emails, or comma-separated string.
     * @param string $subject Email subject line.
     * @param array $blocks Structured content blocks for the template.
     * @param string|null $content Additional email content.
     * @param string|array $cc Carbon copy recipient(s). Can be a single email, array of emails, or comma-separated string.
     * @param string|array $bcc Blind carbon copy recipient(s). Can be a single email, array of emails, or comma-separated string.
     * @param string|null $replyTo Reply-to address.
     * @param string|null $from Sender address. If null, uses default configuration.
     * @param string|null $fromName Sender name. If null, uses default configuration.
     * @param string|null $templateName Template name. Location may be under resources/mail. If null, uses default template.
     * @param array $metadata Additional email metadata.
     */
    public function __construct(
        protected string|array $to,
        protected string $subject,
        protected array $blocks = [],
        protected ?string $content = null,
        protected string|array $cc = [],
        protected string|array $bcc = [],
        protected ?string $replyTo = null,
        protected ?string $from = null,
        protected ?string $fromName = null,
        protected ?string $templateName = null,
        protected array $metadata = []
    ) {
    }

    /**
     * Build the message envelope.
     * 
     * Defines recipients, sender, subject, and metadata for the message.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        $envelopeOptions = $this->getEnvelopeOptions();
        return new Envelope(
            from: $envelopeOptions['from'],
            replyTo: $envelopeOptions['replyTo'],
            to: $envelopeOptions['to'],
            cc: $envelopeOptions['cc'],
            bcc: $envelopeOptions['bcc'],
            subject: $envelopeOptions['subject'],
            metadata: $envelopeOptions['metadata'],
        );
    }

    /**
     * Define the message content.
     * 
     * Specifies the view and data to be passed to the template.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            view: "mail.{$this->getTemplateName()}",
            with: $this->getTemplateData()
        );
    }

    /**
     * Define the message attachments.
     *
     * @return array
     */
    public function attachments(): array
    {
        return [];
    }
    
    // === Helpers ===
    
    /**
     * Get the template data to be passed to the view.
     *
     * @return array
     */
    protected function getTemplateData(): array
    {
        return [
            'subject' => $this->getSubject(),
            'blocks' => $this->blocks,
            'content' => $this->content,
            'metadata' => $this->metadata,
        ];
    }

    protected function getTemplateName(): string
    {
        return $this->templateName ?? 'default';
    }
    
    /**
     * Get the envelope options for the message.
     * 
     * Can return all options or a specific option by key.
     *
     * @param string|null $option Specific option key to retrieve.
     * @return mixed Array with all options or the value of a specific option.
     */
    protected function getEnvelopeOptions(?string $option = null): mixed
    {
        $options = [
            'from' => $this->getFrom(),
            'replyTo' => $this->replyTo ? [new Address($this->replyTo)] : [],
            'to' => $this->getRecipients(),
            'cc' => $this->getAddresses($this->cc),
            'bcc' => $this->getAddresses($this->bcc),
            'subject' => $this->getSubject(),
            'metadata' => $this->metadata,
            'template_name' => $this->getTemplateName(),
            'template_data' => $this->getTemplateData(),
            'is_debug' => $this->isDebugMode(),
        ];

        if($option && Arr::get($options, $option)){
            return Arr::get($options, $option);
        }

        return $options;
    }
    
    /**
     * Normalize email addresses to a consistent array format.
     * 
     * Accepts:
     * - Single email string
     * - Multiple emails separated by commas
     * - Array of emails
     * - Empty value
     *
     * @param mixed $emails Emails in any supported format.
     * @return array Normalized array of emails.
     */
    protected function normalizeEmails(mixed $emails): array
    {
        if (empty($emails)) {
            return [];
        }
        
        if (is_string($emails)) {
            return array_filter(array_map('trim', explode(',', $emails)));
        }
        
        if (is_array($emails)) {
            return array_filter($emails);
        }
        
        return [$emails];
    }
    
    /**
     * Convert normalized emails into Laravel Address objects.
     *
     * @param mixed $emails Emails in any supported format.
     * @return array Array of Address objects.
     */
    protected function getAddresses(mixed $emails): array
    {
        return array_map(
            fn($email) => new Address($email),
            $this->normalizeEmails($emails)
        );
    }
    
    /**
     * Get the sender address and name.
     * 
     * If no custom sender is specified, uses the default configuration.
     *
     * @return Address
     */
    protected function getFrom(): Address
    {
        return new Address(
            $this->from ?? config('mail.from.address'),
            $this->fromName ?? config('mail.from.name')
        );
    }
    
    /**
     * Get the message recipients.
     * 
     * In debug mode, redirects emails to test addresses.
     *
     * @return array Array of Address objects.
     */
    protected function getRecipients(): array
    {
        $recipients = $this->normalizeEmails($this->to);
        
        if ($this->isDebugMode()) {
            return $this->getDebugRecipients($recipients);
        }
        
        return $this->getAddresses($recipients);
    }
    
    /**
     * Get the message subject.
     * 
     * In debug mode, adds a prefix to the subject.
     *
     * @return string
     */
    protected function getSubject(): string
    {
        if (!$this->isDebugMode()) {
            return $this->subject;
        }
        
        $prefix = config('mail.debug.subject_prefix', '[DEBUG]');
        return "{$prefix} {$this->subject}";
    }
    
    /**
     * Determine if the system is in debug mode for emails.
     *
     * @return bool
     */
    protected function isDebugMode(): bool
    {
        return config('mail.debug.enabled', false);
    }
    
    /**
     * Get debug recipients instead of the real recipients.
     * 
     * Logs the redirection if configured.
     *
     * @param array $original Array with original email addresses.
     * @return array Array of Address objects with debug addresses.
     */
    protected function getDebugRecipients(array $original): array
    {
        $debugEmails = config('mail.debug.recipients', []);
        
        if (config('mail.debug.log_envelope', true)) {
            Log::channel(config('mail.debug.log_channel', 'mail'))->info('Email redirected in debug mode', [
                'mailable' => static::class,
                'original_to' => $original,
                'debug_to' => $debugEmails,
                'subject' => $this->subject,
                'metadata' => $this->metadata,
            ]);
        }
        
        return $this->getAddresses($debugEmails);
    }
    
    /**
     * Build the message.
     * 
     * In debug mode with preview enabled, saves an HTML copy of the email.
     *
     * @return static
     */
    public function build()
    {
        if ($this->isDebugMode() && config('mail.debug.save_preview', false)) {
            $this->savePreview();
        }
        
        return parent::build();
    }
    
    /**
     * Save an HTML preview of the email.
     * 
     * Useful for development and debugging. Files are saved with a name
     * based on the subject and timestamp.
     *
     * @return void
     */
    protected function savePreview(): void
    {
        try {
            $path = config('mail.debug.preview_path', storage_path('mail-previews'));
            $filename = Str::slug($this->subject) . '-' . now()->format('Ymd-His') . '.html';
            
            File::ensureDirectoryExists($path);
            File::put("{$path}/{$filename}", $this->render());
            
            Log::channel('mail')->info('Email preview saved', [
                'path' => "{$path}/{$filename}",
                'subject' => $this->subject,
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to save email preview', [
                'error' => $e->getMessage(),
                'subject' => $this->subject,
            ]);
        }
    }
}
