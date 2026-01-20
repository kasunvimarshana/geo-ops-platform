<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Invoice $invoice,
        public string $pdfPath
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $organization = $this->invoice->organization;
        
        return new Envelope(
            from: new Address(
                $organization->email ?? config('mail.from.address'),
                $organization->name ?? config('mail.from.name')
            ),
            replyTo: [
                new Address($organization->email ?? config('mail.from.address'))
            ],
            subject: "Invoice {$this->invoice->invoice_number} from {$organization->name}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice',
            with: [
                'invoice' => $this->invoice,
                'organization' => $this->invoice->organization,
                'customer' => $this->invoice->customer,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];
        
        // Attach the PDF invoice
        if (Storage::disk('public')->exists($this->pdfPath)) {
            $attachments[] = Attachment::fromStorageDisk('public', $this->pdfPath)
                ->as("Invoice_{$this->invoice->invoice_number}.pdf")
                ->withMime('application/pdf');
        }
        
        return $attachments;
    }
}
