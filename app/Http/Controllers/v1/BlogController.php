<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\BlogHero;
use App\Models\BlogAuthor;
use App\Models\BlogPost;
use App\Models\BlogSubscription;
use App\Mail\NewBlogPostMail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class BlogController extends Controller
{
    public function getBlogData()
    {
        // Check cache first
        if (Cache::has('blog_page_data')) {
            return successResponse(Cache::get('blog_page_data'), 'Blog data fetched successfully from cache.');
        }

        // Get from database if cache is empty
        $blogData = $this->getBlogDataFromDatabase();
        
        // Cache the data for guest users (frontend blog list) - expires after 1 year
        Cache::put('blog_page_data', $blogData, now()->addYear());
        
        return successResponse($blogData, 'Blog data fetched successfully from database.');
    }

    public function updateBlogHero(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'subtitle' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422);
        }

        $hero = BlogHero::updateOrCreate(['id' => 1], $validator->validated());
        Cache::forget('blog_page_data');
        return successResponse($hero, 'Blog hero updated successfully');
    }

    public function updateBlogAuthor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'avatar' => 'nullable|string', // Can be base64 or URL
            'bio' => 'nullable|string',
            'social_links' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422);
        }

        $data = $validator->validated();
        $avatarPath = $data['avatar'];

        // Handle base64 avatar upload
        if (isset($data['avatar']) && str_starts_with($data['avatar'], 'data:image')) {
            try {
                $base64Image = $data['avatar'];
                $image_parts = explode(";base64,", $base64Image);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                
                $fileName = 'author_avatar_' . time() . '.' . $image_type;
                $path = 'uploads/blog/author/' . $fileName;
                
                if (!Storage::disk('public')->exists('uploads/blog/author')) {
                    Storage::disk('public')->makeDirectory('uploads/blog/author');
                }
                
                Storage::disk('public')->put($path, $image_base64);
                $avatarPath = asset('storage/' . $path);
            } catch (\Exception $e) {
                return errorResponse('Failed to upload avatar: ' . $e->getMessage());
            }
        }

        $data['avatar'] = $avatarPath;
        
        // Ensure social_links is always an array (default to empty array if not provided)
        if (!isset($data['social_links']) || !is_array($data['social_links'])) {
            $data['social_links'] = [];
        }
        
        $author = BlogAuthor::updateOrCreate(['id' => 1], $data);
        Cache::forget('blog_page_data');
        return successResponse($author, 'Blog author updated successfully');
    }

    public function addBlogPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'content' => 'nullable|array',
            'image' => 'nullable|string',
            'media_type' => 'nullable|in:image,emoji,icon',
            'category' => 'nullable|string',
            'tags' => 'nullable|array',
            'published_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422);
        }

        $data = $validator->validated();
        
        // Ensure content and tags are arrays
        if (!isset($data['content']) || !is_array($data['content'])) {
            $data['content'] = [];
        }
        if (!isset($data['tags']) || !is_array($data['tags'])) {
            $data['tags'] = [];
        }
        
        // Handle media_type and image
        $mediaType = $data['media_type'] ?? 'image';
        
        // If media_type is image and image is base64, upload it
        if ($mediaType === 'image' && isset($data['image']) && str_starts_with($data['image'], 'data:image')) {
            try {
                $base64Image = $data['image'];
                $image_parts = explode(";base64,", $base64Image);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                
                $fileName = 'blog_' . time() . '_' . rand(1000, 9999) . '.' . $image_type;
                $path = 'uploads/blog/' . $fileName;
                
                if (!Storage::disk('public')->exists('uploads/blog')) {
                    Storage::disk('public')->makeDirectory('uploads/blog');
                }
                
                Storage::disk('public')->put($path, $image_base64);
                $data['image'] = asset('storage/' . $path);
            } catch (\Exception $e) {
                return errorResponse('Failed to upload image: ' . $e->getMessage());
            }
        }
        
        $data['media_type'] = $mediaType;
        
        // Generate slug from title, only add random suffix if duplicate exists
        $baseSlug = Str::slug($data['title']);
        $slug = $baseSlug;
        
        // Check if slug already exists, only then add random suffix
        if (BlogPost::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . Str::random(5);
        }
        
        $data['slug'] = $slug;
        $data['author_id'] = 1; // Default author

        $post = BlogPost::create($data);
        
        // Send email notifications to subscribed users
        $this->sendNewPostNotifications($post);
        
        Cache::forget('blog_page_data');
        return successResponse($post, 'Blog post created successfully');
    }

    public function updateBlogPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:blog_posts,id',
            'title' => 'required|string',
            'content' => 'nullable|array',
            'image' => 'nullable|string',
            'media_type' => 'nullable|in:image,emoji,icon',
            'category' => 'nullable|string',
            'tags' => 'nullable|array',
            'published_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422);
        }

        $data = $validator->validated();
        $id = $data['id'];
        unset($data['id']);

        $post = BlogPost::findOrFail($id);
        
        // Ensure content and tags are arrays
        if (!isset($data['content']) || !is_array($data['content'])) {
            $data['content'] = $post->content ?? [];
        }
        if (!isset($data['tags']) || !is_array($data['tags'])) {
            $data['tags'] = $post->tags ?? [];
        }
        
        // Handle media_type and image
        $mediaType = $data['media_type'] ?? 'image';
        
        // If media_type is image and image is base64, upload it
        if ($mediaType === 'image' && isset($data['image']) && str_starts_with($data['image'], 'data:image')) {
            try {
                $base64Image = $data['image'];
                $image_parts = explode(";base64,", $base64Image);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                
                $fileName = 'blog_' . time() . '_' . rand(1000, 9999) . '.' . $image_type;
                $path = 'uploads/blog/' . $fileName;
                
                if (!Storage::disk('public')->exists('uploads/blog')) {
                    Storage::disk('public')->makeDirectory('uploads/blog');
                }
                
                Storage::disk('public')->put($path, $image_base64);
                $data['image'] = asset('storage/' . $path);
            } catch (\Exception $e) {
                return errorResponse('Failed to upload image: ' . $e->getMessage());
            }
        }
        
        $data['media_type'] = $mediaType;
        $post->update($data);
        
        Cache::forget('blog_page_data');
        return successResponse($post, 'Blog post updated successfully');
    }

    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422);
        }

        $email = strtolower(trim($request->input('email')));

        // Check if already subscribed
        $existing = BlogSubscription::where('email', $email)->first();
        
        if ($existing) {
            if ($existing->is_subscribed) {
                return errorResponse('This email is already subscribed', 409);
            } else {
                // Reactivate subscription
                $existing->update(['is_subscribed' => true]);
                return successResponse($existing, 'Subscription reactivated successfully');
            }
        }

        // Create new subscription
        $subscription = BlogSubscription::create([
            'email' => $email,
            'is_subscribed' => true,
        ]);

        return successResponse($subscription, 'Successfully subscribed to blog updates');
    }

    public function unsubscribe(Request $request)
    {
        // Handle both GET (from email link) and POST requests
        $emailParam = $request->input('email') ?? $request->query('email');
        
        if (!$emailParam) {
            return errorResponse('Email parameter is required', 422);
        }

        // Decode if it's base64 encoded (from email link)
        $email = $emailParam;
        $decoded = base64_decode($emailParam, true);
        if ($decoded !== false && filter_var($decoded, FILTER_VALIDATE_EMAIL)) {
            $email = $decoded;
        }

        $email = strtolower(trim($email));

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return errorResponse('Invalid email address', 422);
        }

        $subscription = BlogSubscription::where('email', $email)->first();

        if (!$subscription) {
            return errorResponse('Email not found in our subscription list', 404);
        }

        if (!$subscription->is_subscribed) {
            return errorResponse('This email is already unsubscribed', 409);
        }

        $subscription->update(['is_subscribed' => false]);

        return successResponse($subscription, 'Successfully unsubscribed from blog updates');
    }

    private function sendNewPostNotifications(BlogPost $post)
    {
        try {
            $subscribers = BlogSubscription::where('is_subscribed', true)->get();
            
            foreach ($subscribers as $subscriber) {
                // Queue email notification with subscriber email for unsubscribe link
                Mail::to($subscriber->email)->queue(new NewBlogPostMail($post, $subscriber->email));
            }
        } catch (\Exception $e) {
            \Log::error("Failed to queue blog post notifications: " . $e->getMessage());
            // Don't fail the request if email queuing fails
        }
    }

    public function deleteBlogPost(Request $request)
    {
        $id = $request->input('id');
        BlogPost::where('id', $id)->delete();
        Cache::forget('blog_page_data');
        return successResponse(null, 'Blog post deleted successfully');
    }

    public function getArticle(Request $request, $slug)
    {
        $post = BlogPost::where('slug', $slug)->first();
        
        if (!$post) {
            return errorResponse('Article not found', 404);
        }

        // Calculate read time (average 200 words per minute)
        $wordCount = 0;
        if (is_array($post->content)) {
            foreach ($post->content as $block) {
                if (is_string($block)) {
                    $wordCount += str_word_count($block);
                } elseif (is_array($block) && isset($block['content'])) {
                    $wordCount += str_word_count((string)$block['content']);
                }
            }
        }
        $readTime = max(1, ceil($wordCount / 200)); // At least 1 minute

        // Generate excerpt from content if not exists
        $excerpt = '';
        if (is_array($post->content) && count($post->content) > 0) {
            $firstBlock = $post->content[0];
            if (is_array($firstBlock) && isset($firstBlock['content'])) {
                $firstBlock = $firstBlock['content'];
            }
            if (is_string($firstBlock)) {
                // Remove markdown syntax for excerpt
                $excerpt = preg_replace('/^##\s+/', '', $firstBlock); // Remove heading markers
                $excerpt = preg_replace('/```[\s\S]*?```/', '', $excerpt); // Remove code blocks
                $excerpt = substr(trim($excerpt), 0, 150); // Limit to 150 chars
            }
        }

        $article = [
            'id' => $post->id,
            'slug' => $post->slug,
            'title' => $post->title,
            'excerpt' => $excerpt,
            'date' => $post->published_at ? $post->published_at->format('F j, Y') : '',
            'read_time' => $readTime . ' min read',
            'tags' => $post->tags ?? [],
            'image' => $post->image,
            'media_type' => $post->media_type ?? 'image',
            'media_value' => $post->image, // Use image field for all media types
            'category' => $post->category,
            'content' => $post->content ?? [],
        ];

        return successResponse(['article' => $article], 'Article fetched successfully');
    }

    public function getBlogDataFromDatabase()
    {
        $posts = BlogPost::orderBy('published_at', 'desc')->get()->map(function ($post) {
            // Calculate read time
            $wordCount = 0;
            if (is_array($post->content)) {
                foreach ($post->content as $block) {
                    if (is_string($block)) {
                        $wordCount += str_word_count($block);
                    } elseif (is_array($block) && isset($block['content'])) {
                        $wordCount += str_word_count((string)$block['content']);
                    }
                }
            }
            $readTime = max(1, ceil($wordCount / 200));

            // Generate excerpt
            $excerpt = '';
            if (is_array($post->content) && count($post->content) > 0) {
                $firstBlock = $post->content[0];
                if (is_array($firstBlock) && isset($firstBlock['content'])) {
                    $firstBlock = $firstBlock['content'];
                }
                if (is_string($firstBlock)) {
                    $excerpt = preg_replace('/^##\s+/', '', $firstBlock);
                    $excerpt = preg_replace('/```[\s\S]*?```/', '', $excerpt);
                    $excerpt = substr(trim($excerpt), 0, 150);
                }
            }

            return [
                'id' => $post->id,
                'slug' => $post->slug,
                'title' => $post->title,
                'excerpt' => $excerpt,
                'date' => $post->published_at ? $post->published_at->format('F j, Y') : '',
                'read_time' => $readTime . ' min read',
                'tags' => $post->tags ?? [],
                'content' => $post->content ?? [], // Include content for admin panel
                'image' => $post->image,
                'media_type' => $post->media_type ?? 'image',
                'media_value' => $post->image, // Use image field for all media types
                'category' => $post->category,
            ];
        });

        return [
            'hero' => BlogHero::first(),
            'author' => BlogAuthor::first(),
            'posts' => $posts,
        ];
    }
}
