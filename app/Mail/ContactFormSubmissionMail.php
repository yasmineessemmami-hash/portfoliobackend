<?php

namespace App\Mail;

use App\Models\ContactSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormSubmissionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $submission;

    // Public properties for email template (will be serialized when queued)
    public $name;
    public $email;
    public $company;
    public $reason;
    public $budget;
    public $timeline;
    public $subject;
    public $formMessage;

    /**
     * Create a new message instance.
     */
    public function __construct(ContactSubmission $submission)
    {
        $this->submission = $submission;

        // Set public properties for email template
        $this->name = $submission->name;
        $this->email = $submission->email;
        $this->company = $submission->company;
        $this->reason = $submission->reason;
        $this->budget = $submission->budget;
        $this->timeline = $submission->timeline;
        $this->subject = $submission->subject;
        $this->formMessage = $submission->message;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->submission->subject
            ? 'New Contact Form Submission: ' . $this->submission->subject
            : 'New Contact Form Submission from ' . $this->submission->name;

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Access from submission to ensure data is fresh when job executes
        // Public properties are also available as fallback
        return new Content(
            view: 'emails.contact-form-submission',
            with: [
                'name' => $this->submission->name ?? $this->name,
                'email' => $this->submission->email ?? $this->email,
                'company' => $this->submission->company ?? $this->company,
                'reason' => $this->submission->reason ?? $this->reason,
                'budget' => $this->submission->budget ?? $this->budget,
                'timeline' => $this->submission->timeline ?? $this->timeline,
                'subject' => $this->submission->subject ?? $this->subject,
                'formMessage' => $this->submission->message ?? $this->formMessage,
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
        return [];
    }
}
