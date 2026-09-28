<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\ProjectsHero;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ProjectsController extends Controller
{
    public function getProjectsData()
    {
        if (Cache::has('projects_page_data')) {
            return successResponse(Cache::get('projects_page_data'), 'Projects data fetched successfully from cache.');
        }

        $projectsData = $this->getProjectsDataFromDatabase();
        Cache::put('projects_page_data', $projectsData, now()->addYear());
        return successResponse($projectsData, 'Projects data fetched successfully from database.');
    }

    public function updateProjectsHero(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'subtitle' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422);
        }

        $hero = ProjectsHero::updateOrCreate(['id' => 1], $validator->validated());
        Cache::forget('projects_page_data');
        return successResponse($hero, 'Projects hero updated successfully');
    }

    public function updateProjectItem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|integer',
            'title' => 'required|string',
            'description' => 'required|string',
            'image' => 'required|string', // required for new projects, string for base64 or url
            'tech_stack' => 'required|array',
            'tech_stack.*' => 'string',
            'github_url' => 'nullable|string',
            'live_url' => 'nullable|string',
            'contact_email' => 'nullable|string',
            'is_featured' => 'required|boolean',
            'sort_order' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422);
        }

        $data = $validator->validated();
        $id = $data['id'] ?? null;
        unset($data['id']);

        $imagePath = $data['image'];

        // Handle base64 image upload
        if ($data['image'] && str_starts_with($data['image'], 'data:image')) {
            try {
                $base64Image = $data['image'];
                $image_parts = explode(";base64,", $base64Image);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                
                $fileName = 'project_' . time() . '_' . rand(1000, 9999) . '.' . $image_type;
                $path = 'uploads/projects/' . $fileName;
                
                if (!\Illuminate\Support\Facades\Storage::disk('public')->exists('uploads/projects')) {
                    \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory('uploads/projects');
                }
                
                \Illuminate\Support\Facades\Storage::disk('public')->put($path, $image_base64);
                $imagePath = asset('storage/' . $path);
            } catch (\Exception $e) {
                return errorResponse('Failed to upload image: ' . $e->getMessage());
            }
        }

        $data['image'] = $imagePath;
        $data['image_type'] = 'url'; // Always url for uploaded files

        $project = Project::updateOrCreate(['id' => $id], $data);
        
        Cache::forget('projects_page_data');
        Cache::forget('home_page_data'); // If it's featured
        
        return successResponse($project, 'Project updated successfully');
    }

    public function deleteProjectItem(Request $request)
    {
        $id = $request->input('id');
        Project::where('id', $id)->delete();
        Cache::forget('projects_page_data');
        Cache::forget('home_page_data');
        return successResponse(null, 'Project deleted successfully');
    }

    public function getProjectsDataFromDatabase()
    {
        $hero = ProjectsHero::first();
        $projects = Project::orderBy('sort_order')->get();

        return [
            'hero' => $hero,
            'projects' => $projects,
        ];
    }
}
