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
    protected $blocks = [];
    protected $content = [];
    protected $options = [];
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
        string|array $to,
        array $blocks = [],
        ?string $content = null,
        array $options = [],
        ?array $attachments = [],
    ) {
        $this->blocks = $blocks;
        $this->content = $content;
        $this->options = array_merge($options, [
            'to' => $to,
        ]);
        $this->attachments = $attachments;
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
        Log::channel('mail_logger')->info('Email redirected in debug mode', [
            'mailable' => static::class,
            'recipients' => $this->extractEmailFromAddress('to'),
            'original_recipients' => $this->getOption('original_to'),
            'is_debug' => $this->isDebugMode(),
            'subject' => $this->options['subject'],
            'original_subject' => $this->getOption('original_subject'),
            'metadata' => $this->options['metadata'] ?? [],
        ]);

        return new Envelope(
            from: $this->getFrom(),
            replyTo: $this->getReplyTo(),
            to: $this->getRecipients(),
            cc: $this->getAddresses($this->cc),
            bcc: $this->getAddresses($this->bcc),
            subject: $this->getSubject(),
            metadata: $this->metadata,
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
        return $this->attachments;
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
            'metadata' => $this->options['metadata'] ?? [],
        ];
    }

    protected function getTemplateName(): string
    {
        return $this->options['templateName'] ?? 'default';
    }

    /**
     * Get the mailable options for the message.
     * 
     * Can return all options or a specific option by key.
     *
     * @param string|null $option Specific option key to retrieve.
     * @return mixed Array with all options or the value of a specific option.
     */
    public function getOption(?string $option = null): mixed
    {
        $options = [
            'from' => $this->getFrom(),
            'replyTo' => $this->getReplyTo(),
            'to' => $this->getRecipients(),
            'original_to' => $this->normalizeEmails($this->options['to'] ?? []),
            'cc' => $this->getAddresses($this->options['cc'] ?? []),
            'bcc' => $this->getAddresses($this->options['bcc'] ?? []),
            'subject' => $this->getSubject(),
            'original_subject' => $this->options['subject'] ?? '',
            'metadata' => $this->options['metadata'] ?? [],
            'template_name' => $this->getTemplateName(),
            'template_data' => $this->getTemplateData(),
            'is_debug' => $this->isDebugMode(),
        ];

        if ($option && Arr::has($options, $option)) {
            return Arr::get($options, $option);
        } elseif ($option) {
            return null;
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

    protected function getReplyTo()
    {
        return $this->getAddresses(Arr::get($this->options, 'replyTo'));
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
            $this->options['from'] ?? config('mail.from.address'),
            $this->options['fromName'] ?? config('mail.from.name')
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
        $recipients = $this->normalizeEmails($this->options['to'] ?? []);

        if ($this->isDebugMode()) {
            return $this->getDebugRecipients();
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
            return $this->options['subject'];
        }

        $prefix = config('mail.debug.subject_prefix', '[DEBUG]');
        return "{$prefix} {$this->options['subject']}";
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
    protected function getDebugRecipients(): array
    {
        $debugEmails = config('mail.debug.recipients', []);

        return $this->getAddresses($debugEmails);
    }

    public function extractEmailFromAddress(mixed $address): string|array|null
    {
        if ($address instanceof Address) {
            return $address->address;
        }

        if (is_array($address)) {
            return array_map(fn($addr) => $this->extractEmailFromAddress($addr), $address);
        }

        if (is_string($address)) {
            $options = $this->getOption();
            if (isset($options[$address])) {
                return $this->extractEmailFromAddress($options[$address]);
            }
        }

        if ($address instanceof \Illuminate\Support\Collection) {
            return $address->map(fn($addr) => $this->extractEmailFromAddress($addr))->toArray();
        }

        return null;
    }
}
