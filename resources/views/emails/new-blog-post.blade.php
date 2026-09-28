<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Blog Post: {{ $post->title }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h1 style="color: #2c3e50; margin-top: 0;">New Blog Post Published!</h1>
        <p style="margin-bottom: 0;">We're excited to share a new article with you.</p>
    </div>

    <div style="background-color: #ffffff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
        <h2 style="color: #2c3e50; margin-top: 0;">{{ $post->title }}</h2>
        
        @if(!empty($excerpt))
            <p style="color: #666; font-size: 16px;">{{ $excerpt }}...</p>
        @endif

        @if($post->published_at)
            <p style="color: #999; font-size: 14px; margin-top: 10px;">
                Published: {{ $post->published_at->format('F j, Y') }}
            </p>
        @endif

        <div style="margin-top: 30px;">
            <a href="{{ $articleUrl }}" style="display: inline-block; background-color: #3498db; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                Read Article →
            </a>
        </div>
    </div>

    <div style="text-align: center; color: #999; font-size: 12px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0;">
        <p>You're receiving this email because you subscribed to blog updates.</p>
        <p>Thank you for being part of our community!</p>
        <p style="margin-top: 20px; color: #888;">
            If this email was delivered to the wrong person or you need to unsubscribe, 
            <a href="{{ $unsubscribeUrl ?? url('/api/v1/blog/unsubscribe') }}" style="color: #3498db; text-decoration: underline;">click here to unsubscribe</a>.
        </p>
    </div>
</body>
</html>

