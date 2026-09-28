<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\AboutHero;
use App\Models\AboutIntroduction;
use App\Models\AboutStat;
use App\Models\AboutValue;
use App\Models\AboutService;
use App\Models\AboutWorkProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class AboutController extends Controller
{
    public function getAboutData()
    {
        if (Cache::has('about_page_data')) {
            return successResponse(Cache::get('about_page_data'), 'About data fetched successfully from cache.');
        }

        $aboutData = $this->getAboutDataFromDatabase();
        Cache::put('about_page_data', $aboutData, now()->addYear());
        return successResponse($aboutData, 'About data fetched successfully from database.');
    }

    public function updateAboutHero(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'subtitle' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422);
        }

        $hero = AboutHero::updateOrCreate(['id' => 1], $validator->validated());
        Cache::forget('about_page_data');
        return successResponse($hero, 'About hero updated successfully');
    }

    public function updateAboutStats(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stats' => 'present|array',
            'stats.*.key' => 'required|string',
            'stats.*.value' => 'required|string',
            'stats.*.label' => 'required|string',
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422);
        }

        $data = $validator->validated();

        try {
            DB::beginTransaction();
            AboutStat::truncate();
            foreach ($data['stats'] as $index => $stat) {
                AboutStat::create(array_merge($stat, ['sort_order' => $index]));
            }
            DB::commit();
            Cache::forget('about_page_data');
            return successResponse(null, 'About stats updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return errorResponse('Failed to update stats');
        }
    }

    public function updateAboutIntroduction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatarImage' => 'nullable|string',
            'availabilityActive' => 'required|boolean',
            'availabilityText' => 'nullable|string',
            'fullName' => 'required|string',
            'roleTitle' => 'required|string',
            'paragraphs' => 'present|array',
            'techStack' => 'present|array',
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422);
        }

        $data = $validator->validated();

        $avatarPath = $data['avatarImage'];

        // Handle base64 image upload
        if ($data['avatarImage'] && str_starts_with($data['avatarImage'], 'data:image')) {
            try {
                $base64Image = $data['avatarImage'];
                $image_parts = explode(";base64,", $base64Image);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);

                $fileName = 'avatar_' . time() . '.' . $image_type;
                $path = 'uploads/about/' . $fileName;

                if (!\Illuminate\Support\Facades\Storage::disk('public')->exists('uploads/about')) {
                    \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory('uploads/about');
                }

                \Illuminate\Support\Facades\Storage::disk('public')->put($path, $image_base64);
                $avatarPath = asset('storage/' . $path);
            } catch (\Exception $e) {
                return errorResponse('Failed to upload image: ' . $e->getMessage());
            }
        }

        $intro = AboutIntroduction::updateOrCreate(
            ['id' => 1],
            [
                'avatar_image' => $avatarPath,
                'availability_active' => $data['availabilityActive'],
                'availability_text' => $data['availabilityText'],
                'full_name' => $data['fullName'],
                'role_title' => $data['roleTitle'],
                'paragraphs' => $data['paragraphs'],
                'tech_stack' => $data['techStack'],
            ]
        );

        Cache::forget('about_page_data');
        return successResponse([
            'introduction' => [
                'avatar' => ['image' => $intro->avatar_image],
                'availability' => [
                    'is_active' => $intro->availability_active,
                    'text' => $intro->availability_text,
                ],
                'full_name' => $intro->full_name,
                'role_title' => $intro->role_title,
                'paragraphs' => $intro->paragraphs,
                'tech_stack' => $intro->tech_stack,
            ]
        ], 'About introduction updated successfully');
    }

    public function updateAboutServices(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'services' => 'present|array',
            'services.*.title' => 'required|string',
            'services.*.description' => 'required|string',
            'services.*.features' => 'present|array',
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422);
        }

        $data = $validator->validated();

        try {
            DB::beginTransaction();
            AboutService::truncate();
            foreach ($data['services'] as $index => $service) {
                AboutService::create(array_merge($service, ['sort_order' => $index]));
            }
            DB::commit();
            Cache::forget('about_page_data');
            return successResponse(null, 'About services updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return errorResponse('Failed to update services');
        }
    }

    public function updateAboutWorkProcess(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'steps' => 'present|array',
            'steps.*.step' => 'required|string',
            'steps.*.title' => 'required|string',
            'steps.*.description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422);
        }

        $data = $validator->validated();

        try {
            DB::beginTransaction();
            AboutWorkProcess::truncate();
            foreach ($data['steps'] as $index => $step) {
                AboutWorkProcess::create(array_merge($step, ['sort_order' => $index]));
            }
            DB::commit();
            Cache::forget('about_page_data');
            return successResponse(null, 'About work process updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return errorResponse('Failed to update work process');
        }
    }

    public function updateAboutValues(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'values' => 'present|array',
            'values.*.key' => 'required|string',
            'values.*.title' => 'required|string',
            'values.*.description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422);
        }

        $data = $validator->validated();

        try {
            DB::beginTransaction();
            AboutValue::truncate();
            foreach ($data['values'] as $index => $value) {
                AboutValue::create(array_merge($value, ['sort_order' => $index]));
            }
            DB::commit();
            Cache::forget('about_page_data');
            return successResponse(null, 'About values updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return errorResponse('Failed to update values');
        }
    }

    public function getAboutDataFromDatabase()
    {
        $hero = AboutHero::first();
        $stats = AboutStat::orderBy('sort_order')->get();
        $introduction = AboutIntroduction::first();
        $services = AboutService::orderBy('sort_order')->get();
        $workProcess = AboutWorkProcess::orderBy('sort_order')->get();
        $values = AboutValue::orderBy('sort_order')->get();

        return [
            'hero' => $hero,
            'stats' => $stats,
            'introduction' => $introduction ? [
                'avatar' => ['image' => $introduction->avatar_image],
                'availability' => [
                    'is_active' => $introduction->availability_active,
                    'text' => $introduction->availability_text,
                ],
                'full_name' => $introduction->full_name,
                'role_title' => $introduction->role_title,
                'paragraphs' => $introduction->paragraphs,
                'tech_stack' => $introduction->tech_stack,
            ] : null,
            'services' => $services,
            'work_process' => $workProcess,
            'values' => $values,
        ];
    }
}
