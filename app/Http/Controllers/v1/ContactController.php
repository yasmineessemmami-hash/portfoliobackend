<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\ContactHero;
use App\Models\ContactInfo;
use App\Models\ContactSocialLink;
use App\Models\ContactSubmission;
use App\Models\Admin;
use App\Mail\ContactFormSubmissionMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function getContactData()
    {
        // Check cache first
        if (Cache::has('contact_page_data')) {
            return successResponse(Cache::get('contact_page_data'), 'Contact data fetched successfully from cache.');
        }

        // Get from database if cache is empty
        $contactData = $this->getContactDataFromDatabase();
        
        // Cache the data for guest users - expires after 1 year
        Cache::put('contact_page_data', $contactData, now()->addYear());
        
        return successResponse($contactData, 'Contact data fetched successfully from database.');
    }

    public function updateContactHero(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'subtitle' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422);
        }

        $hero = ContactHero::updateOrCreate(['id' => 1], $validator->validated());
        Cache::forget('contact_page_data');
        return successResponse($hero, 'Contact hero updated successfully');
    }

    public function updateContactInfos(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'infos' => 'required|array',
            'infos.*.label' => 'required|string',
            'infos.*.value' => 'required|string',
            'infos.*.icon_key' => 'required|string',
            'infos.*.type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422);
        }

        $data = $validator->validated();

        try {
            DB::beginTransaction();
            ContactInfo::query()->delete();
            foreach ($data['infos'] as $index => $info) {
                ContactInfo::create(array_merge($info, ['sort_order' => $index]));
            }
            DB::commit();
            Cache::forget('contact_page_data');
            return successResponse(null, 'Contact information updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return errorResponse('Failed to update contact info');
        }
    }

    public function submitContactForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'company' => 'nullable|string|max:255',
            'reason' => 'required|string|max:255',
            'budget' => 'required|string|max:255',
            'timeline' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422);
        }

        try {
            // Store submission in database
            $submission = ContactSubmission::create($validator->validated());

            // Send email to all admins
            $admins = Admin::all();
            if ($admins->count() > 0) {
                foreach ($admins as $admin) {
                    if ($admin->email) {
                        try {
                            Mail::to($admin->email)->send(new ContactFormSubmissionMail($submission));
                        } catch (\Exception $emailException) {
                            Log::error("Failed to send contact form email to {$admin->email}: " . $emailException->getMessage());
                            // Continue sending to other admins even if one fails
                        }
                    }
                }
            }

            return successResponse($submission, 'Contact form submitted successfully');
        } catch (\Exception $e) {
            return errorResponse('Failed to submit contact form: ' . $e->getMessage());
        }
    }

    public function getContactSubmissions(Request $request)
    {
        try {
            $submissions = ContactSubmission::orderBy('created_at', 'desc')->get();
            return successResponse($submissions, 'Contact submissions fetched successfully');
        } catch (\Exception $e) {
            return errorResponse('Failed to fetch contact submissions: ' . $e->getMessage());
        }
    }

    public function markSubmissionAsRead(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:contact_submissions,id',
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422);
        }

        try {
            $submission = ContactSubmission::findOrFail($request->id);
            $submission->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
            return successResponse($submission, 'Submission marked as read');
        } catch (\Exception $e) {
            return errorResponse('Failed to update submission: ' . $e->getMessage());
        }
    }

    public function updateContactSocialLinks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'links' => 'required|array',
            'links.*.label' => 'required|string',
            'links.*.url' => 'required|string',
            'links.*.icon_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422);
        }

        $data = $validator->validated();

        try {
            DB::beginTransaction();
            ContactSocialLink::query()->delete();
            foreach ($data['links'] as $index => $link) {
                // Remove 'id' field if present (it's generated by database)
                unset($link['id']);
                ContactSocialLink::create(array_merge($link, ['sort_order' => $index]));
            }
            DB::commit();
            Cache::forget('contact_page_data');
            return successResponse(null, 'Contact social links updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return errorResponse('Failed to update contact social links: ' . $e->getMessage());
        }
    }

    public function getContactDataFromDatabase()
    {
        $infos = ContactInfo::orderBy('sort_order')->get()->map(function ($info) {
            return [
                'key' => strtolower(str_replace(' ', '_', $info->label)),
                'icon_key' => $info->icon_key,
                'label' => $info->label,
                'value' => $info->value,
                'href' => $info->type === 'email' ? 'mailto:' . $info->value : ($info->type === 'phone' ? 'tel:' . $info->value : ($info->type === 'url' ? $info->value : null)),
                'type' => $info->type === 'url' ? 'link' : 'text',
            ];
        });

        $socialLinks = ContactSocialLink::orderBy('sort_order')->get()->map(function ($link) {
            return [
                'key' => strtolower(str_replace(' ', '_', $link->label)),
                'icon_key' => $link->icon_key,
                'label' => $link->label,
                'url' => $link->url,
            ];
        });

        // Default form fields
        $form = [
            'fields' => [
                [
                    'name' => 'name',
                    'label' => 'Name',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'Your name',
                ],
                [
                    'name' => 'email',
                    'label' => 'Email',
                    'type' => 'email',
                    'required' => true,
                    'placeholder' => 'your.email@example.com',
                ],
                [
                    'name' => 'company',
                    'label' => 'Company',
                    'type' => 'text',
                    'required' => false,
                    'placeholder' => 'Your company name',
                ],
                [
                    'name' => 'reason',
                    'label' => 'Reason for Contact',
                    'type' => 'select',
                    'required' => true,
                    'options' => [
                        ['value' => '', 'label' => 'Select a reason'],
                        ['value' => 'new-project', 'label' => 'New Project'],
                        ['value' => 'consultation', 'label' => 'Consultation'],
                        ['value' => 'job-opportunity', 'label' => 'Job Opportunity'],
                        ['value' => 'collaboration', 'label' => 'Collaboration'],
                        ['value' => 'other', 'label' => 'Other'],
                    ],
                ],
                [
                    'name' => 'budget',
                    'label' => 'Budget Range',
                    'type' => 'select',
                    'required' => true,
                    'options' => [
                        ['value' => '', 'label' => 'Select budget range'],
                        ['value' => 'under-1k', 'label' => 'Under $1,000'],
                        ['value' => '1k-5k', 'label' => '$1,000 - $5,000'],
                        ['value' => '5k-10k', 'label' => '$5,000 - $10,000'],
                        ['value' => '10k-25k', 'label' => '$10,000 - $25,000'],
                        ['value' => '25k-plus', 'label' => '$25,000+'],
                        ['value' => 'not-sure', 'label' => 'Not Sure Yet'],
                    ],
                ],
                [
                    'name' => 'timeline',
                    'label' => 'Timeline',
                    'type' => 'select',
                    'required' => true,
                    'options' => [
                        ['value' => '', 'label' => 'Select timeline'],
                        ['value' => 'asap', 'label' => 'ASAP'],
                        ['value' => '1-2-weeks', 'label' => '1-2 Weeks'],
                        ['value' => '1-month', 'label' => 'Within 1 Month'],
                        ['value' => '2-3-months', 'label' => '2-3 Months'],
                        ['value' => 'flexible', 'label' => 'Flexible'],
                    ],
                ],
                [
                    'name' => 'message',
                    'label' => 'Message',
                    'type' => 'textarea',
                    'required' => true,
                    'placeholder' => 'Tell me about your project or just say hi...',
                ],
            ],
        ];

        return [
            'hero' => ContactHero::first(),
            'contact_info' => $infos,
            'social_links' => $socialLinks,
            'form' => $form,
        ];
    }
}
