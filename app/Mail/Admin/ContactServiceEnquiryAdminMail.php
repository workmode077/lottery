<?php

namespace App\Mail\Admin;

use App\Models\ContactServiceEnquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactServiceEnquiryAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public $enquiry;

   
    public function __construct(ContactServiceEnquiry $enquiry)
    {
        $this->enquiry = $enquiry;
    }


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New contact Service Enquiry Received - ' . config('app.name'),
        );
    }


    public function content(): Content
    {
        return new Content(
            view: 'mail.admin.contact-service-enquiry-admin-mail',
            with: ['enquiry' => $this->enquiry]
        );
    }


    public function attachments(): array
    {
        return [];
    }
}
