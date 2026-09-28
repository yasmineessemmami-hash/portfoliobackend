<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\SkillsHero;
use App\Models\SkillCategory;
use App\Models\Skill;
use App\Models\LearningFocus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

use App\Models\SkillsSocialLink;
use Illuminate\Support\Facades\Storage;

class SkillsController extends Controller
{
    public function getSkillsData()
    {
        if (Cache::has('skills_page_data')) {
            return successResponse(Cache::get('skills_page_data'), 'Skills data fetched successfully from cache.');
        }

        $skillsData = $this->getSkillsDataFromDatabase();
        Cache::put('skills_page_data', $skillsData, now()->addYear());
        return successResponse($skillsData, 'Skills data fetched successfully from database.');
    }

    /**
     * Download CV file
     */
    public function downloadCV()
    {
        $hero = SkillsHero::first();
        if (!$hero || !$hero->cv_file_url) {
            return errorResponse('CV file not found', 404);
        }

        // The cv_file_url is stored as an absolute URL (e.g. http://127.0.0.1:8000/storage/uploads/cv/...)
        // We need to convert it to a local storage path
        $path = str_replace(asset('storage/'), '', $hero->cv_file_url);

        if (Storage::disk('public')->exists($path)) {
            // Use full_name from site settings for the filename
            $siteSetting = \App\Models\SiteSetting::first();
            $fullName = $siteSetting ? $siteSetting->full_name : 'Portfolio';

            // Get original extension
            $extension = pathinfo($hero->cv_file_url, PATHINFO_EXTENSION);
            $filename = "{$fullName} CV.{$extension}";

            return Storage::disk('public')->download($path, $filename);
        }

        return errorResponse('File does not exist on disk', 404);
    }

    public function updateSkillsHero(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'subtitle' => 'nullable|string',
            'description' => 'nullable|string',
            'cv_label' => 'required|string',
            'cv_file' => 'nullable|string', // Base64 file
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422);
        }

        $data = $validator->validated();
        $cvFile = $data['cv_file'] ?? null;
        unset($data['cv_file']);

        $hero = SkillsHero::first();
        $cvPath = $hero ? $hero->cv_file_url : null;

        // Handle base64 CV upload
        if ($cvFile && str_contains($cvFile, ';base64,')) {
            try {
                $file_parts = explode(";base64,", $cvFile);
                $file_type_aux = explode("/", $file_parts[0]);
                $extension = str_contains($file_parts[0], 'msword') ? 'doc' : (str_contains($file_parts[0], 'wordprocessingml') ? 'docx' : ($file_type_aux[1] ?? 'pdf'));
                $file_base64 = base64_decode($file_parts[1]);

                $fileName = 'cv_' . time() . '.' . $extension;
                $path = 'uploads/cv/' . $fileName;

                if (!Storage::disk('public')->exists('uploads/cv')) {
                    Storage::disk('public')->makeDirectory('uploads/cv');
                }

                Storage::disk('public')->put($path, $file_base64);
                $cvPath = asset('storage/' . $path);
            } catch (\Exception $e) {
                return errorResponse('Failed to upload CV: ' . $e->getMessage());
            }
        } else if ($cvFile === null && $hero) {
            // Keep existing if not sending new one
            $cvPath = $hero->cv_file_url;
        }

        $data['cv_file_url'] = $cvPath;

        $hero = SkillsHero::updateOrCreate(['id' => 1], $data);
        Cache::forget('skills_page_data');
        return successResponse($hero, 'Skills hero and CV updated successfully');
    }

    public function updateSkillsSocialLinks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'socials' => 'present|array',
            'socials.*.platform' => 'required|string',
            'socials.*.url' => 'required|string',
            'socials.*.icon_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422);
        }

        $data = $validator->validated();

        try {
            DB::beginTransaction();
            SkillsSocialLink::truncate();
            foreach ($data['socials'] as $index => $social) {
                SkillsSocialLink::create(array_merge($social, ['sort_order' => $index]));
            }
            DB::commit();
            Cache::forget('skills_page_data');
            return successResponse(null, 'Skills social links updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return errorResponse('Failed to update social links');
        }
    }

    public function updateSkillCategories(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'skill_categories' => 'required|array',
            'skill_categories.*.key' => 'required|string',
            'skill_categories.*.icon_key' => 'required|string',
            'skill_categories.*.title' => 'required|string',
            'skill_categories.*.description' => 'nullable|string',
            'skill_categories.*.skills' => 'required|array',
            'skill_categories.*.skills.*' => 'string',
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422);
        }

        $data = $validator->validated();

        try {
            DB::beginTransaction();
            SkillCategory::query()->delete();
            foreach ($data['skill_categories'] as $index => $categoryData) {
                $category = SkillCategory::create([
                    'key' => $categoryData['key'],
                    'icon_key' => $categoryData['icon_key'],
                    'title' => $categoryData['title'],
                    'description' => $categoryData['description'],
                    'sort_order' => $index,
                ]);

                foreach ($categoryData['skills'] as $skillIndex => $skillName) {
                    Skill::create([
                        'category_id' => $category->id,
                        'name' => $skillName,
                        'sort_order' => $skillIndex,
                    ]);
                }
            }
            DB::commit();
            Cache::forget('skills_page_data');
            return successResponse(null, 'Skill categories updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return errorResponse('Failed to update skill categories');
        }
    }

    public function updateLearningFocus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'topics' => 'required|array',
            'topics.*' => 'string',
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422);
        }

        $learningFocus = LearningFocus::updateOrCreate(['id' => 1], $validator->validated());
        Cache::forget('skills_page_data');
        return successResponse($learningFocus, 'Learning focus updated successfully');
    }

    public function getSkillsDataFromDatabase()
    {
        $hero = SkillsHero::first();
        $categories = SkillCategory::with('skills')->orderBy('sort_order')->get();
        $learningFocus = LearningFocus::first();
        $socialLinks = SkillsSocialLink::orderBy('sort_order')->get();

        return [
            'hero' => $hero,
            'skill_categories' => $categories->map(function ($category) {
                return [
                    'key' => $category->key,
                    'icon_key' => $category->icon_key,
                    'title' => $category->title,
                    'description' => $category->description,
                    'skills' => $category->skills->pluck('name')->toArray(),
                ];
            }),
            'learning_focus' => $learningFocus,
            'social_links' => $socialLinks,
        ];
    }
}
