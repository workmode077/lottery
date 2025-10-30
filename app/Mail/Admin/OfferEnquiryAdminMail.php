<?php

namespace App\Mail\Admin;

use App\Models\OfferEnquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OfferEnquiryAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public $enquiry;

   
    public function __construct(OfferEnquiry $enquiry)
    {
        $this->enquiry = $enquiry;
    }


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Offer Enquiry Received - ' . config('app.name'),
        );
    }


    public function content(): Content
    {
        return new Content(
            view: 'mail.admin.offer-enquiry-admin-mail',
            with: ['enquiry' => $this->enquiry]
        );
    }


    public function attachments(): array
    {
        return [];
    }
}
