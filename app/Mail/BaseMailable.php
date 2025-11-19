<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class BaseMailable extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
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
        protected array $metadata = []
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->getFrom(),
            replyTo: $this->replyTo ? [new Address($this->replyTo)] : [],
            to: $this->getRecipients(),
            cc: $this->getAddresses($this->cc),
            bcc: $this->getAddresses($this->bcc),
            subject: $this->getSubject(),
            metadata: $this->metadata,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.default',
            with: [
                'blocks' => $this->blocks,
                'content' => $this->content,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
    
    // === Helpers ===
    
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
    
    protected function getAddresses(mixed $emails): array
    {
        return array_map(
            fn($email) => new Address($email),
            $this->normalizeEmails($emails)
        );
    }
    
    protected function getFrom(): Address
    {
        return new Address(
            $this->from ?? config('mail.from.address'),
            $this->fromName ?? config('mail.from.name')
        );
    }
    
    protected function getRecipients(): array
    {
        $recipients = $this->normalizeEmails($this->to);
        
        if ($this->isDebugMode()) {
            return $this->getDebugRecipients($recipients);
        }
        
        return $this->getAddresses($recipients);
    }
    
    protected function getSubject(): string
    {
        if (!$this->isDebugMode()) {
            return $this->subject;
        }
        
        $prefix = config('mail.debug.subject_prefix', '[DEBUG]');
        return "{$prefix} {$this->subject}";
    }
    
    protected function isDebugMode(): bool
    {
        return config('mail.debug.enabled', false);
    }
    
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
    
    public function build()
    {
        if ($this->isDebugMode() && config('mail.debug.save_preview', false)) {
            $this->savePreview();
        }
        
        return parent::build();
    }
    
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
