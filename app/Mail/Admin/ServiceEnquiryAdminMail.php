<?php

namespace App\Mail\Admin;

use App\Models\ServiceEnquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ServiceEnquiryAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public $enquiry;

   
    public function __construct(ServiceEnquiry $enquiry)
    {
        $this->enquiry = $enquiry;
    }


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Service Enquiry Received - ' . config('app.name'),
        );
    }


    public function content(): Content
    {
        return new Content(
            view: 'mail.admin.service-enquiry-admin-mail',
            with: ['enquiry' => $this->enquiry]
        );
    }


    public function attachments(): array
    {
        return [];
    }
}
