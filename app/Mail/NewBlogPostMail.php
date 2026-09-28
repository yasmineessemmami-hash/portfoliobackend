<?php

namespace App\Mail;

use App\Models\BlogPost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewBlogPostMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $post;
    public $articleUrl;
    public $unsubscribeUrl = '';

    /**
     * Create a new message instance.
     */
    public function __construct(BlogPost $post, string $subscriberEmail = '')
    {
        $this->post = $post;
        $this->articleUrl = url("/blog/{$post->slug}");
        // Generate unsubscribe URL with encoded email
        if ($subscriberEmail) {
            $encodedEmail = base64_encode($subscriberEmail);
            $this->unsubscribeUrl = url("/api/v1/blog/unsubscribe?email=" . urlencode($encodedEmail));
        } else {
            $this->unsubscribeUrl = url("/api/v1/blog/unsubscribe");
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Blog Post: ' . $this->post->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Generate excerpt
        $excerpt = '';
        if (is_array($this->post->content) && count($this->post->content) > 0) {
            $firstBlock = $this->post->content[0];
            $excerpt = preg_replace('/^##\s+/', '', $firstBlock);
            $excerpt = preg_replace('/```[\s\S]*?```/', '', $excerpt);
            $excerpt = substr(trim($excerpt), 0, 200);
        }

        // Ensure unsubscribeUrl is set (fallback for safety)
        if (empty($this->unsubscribeUrl)) {
            $this->unsubscribeUrl = url("/api/v1/blog/unsubscribe");
        }

        return new Content(
            view: 'emails.new-blog-post',
            with: [
                'post' => $this->post,
                'articleUrl' => $this->articleUrl,
                'excerpt' => $excerpt,
                'unsubscribeUrl' => $this->unsubscribeUrl,
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
