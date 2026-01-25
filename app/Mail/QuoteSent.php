<?php

namespace App\Mail;

use App\Models\Quote;
use App\Services\PdfGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteSent extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Quote $quote,
        public string $message = '',
        public bool $attachPdf = true
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $tenant = tenant();

        return new Envelope(
            from: new Address(
                $tenant->email ?? config('mail.from.address'),
                $tenant->name ?? config('mail.from.name')
            ),
            subject: "Quote {$this->quote->quote_number} from " . ($tenant->name ?? config('app.name')),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.quote-sent',
            with: [
                'quote' => $this->quote,
                'message' => $this->message,
                'company' => [
                    'name' => tenant()->name ?? config('app.name'),
                    'email' => tenant()->email ?? config('mail.from.address'),
                    'phone' => tenant()->phone ?? '',
                ],
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if (!$this->attachPdf) {
            return [];
        }

        $pdfGenerator = app(PdfGenerator::class);
        $pdf = $pdfGenerator->generateQuotePdf($this->quote);

        return [
            Attachment::fromData(fn() => $pdf->output(), "Quote-{$this->quote->quote_number}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
