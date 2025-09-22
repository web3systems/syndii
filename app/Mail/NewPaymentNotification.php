<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Traits\InvoiceGeneratorTrait;
use App\Models\Payment;

class NewPaymentNotification extends Mailable
{
    use Queueable, SerializesModels, InvoiceGeneratorTrait;

    public $order;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Payment $order)
    {
        $this->order = $order;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'New Payment Notification',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'emails.order.new-payment',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        try {

            $fileName = $this->invoiceAttachment($this->order->order_id);
            
            $invoicePath = Storage::disk('audio')->path("{$fileName}.pdf");
            
            if (Storage::disk('audio')->exists("{$fileName}.pdf")) {
                return [
                    Attachment::fromPath($invoicePath)
                        ->as("{$fileName}.pdf")
                        ->withMime('application/pdf')
                ];
            }
            
            return [];
        } catch (\Exception $e) {
            \Log::error('Failed to generate invoice attachment: ' . $e->getMessage());
            return [];
        
        }
    }
}
