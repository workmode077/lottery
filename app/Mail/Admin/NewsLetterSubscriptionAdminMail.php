<?php

namespace App\Mail\Admin;

use App\Models\NewsLetterSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsLetterSubscriptionAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public $enquiry;

   
    public function __construct(NewsLetterSubscription $enquiry)
    {
        $this->enquiry = $enquiry;
    }


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Subscription Received - ' . config('app.name'),
        );
    }


    public function content(): Content
    {
        return new Content(
            view: 'mail.admin.news-letter-subscription-admin-mail',
            with: ['enquiry' => $this->enquiry]
        );
    }


    public function attachments(): array
    {
        return [];
    }
}
