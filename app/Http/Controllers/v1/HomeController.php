<?php

namespace App\Http\Controllers\v1;

use App\Models\HomeHero;
use App\Models\MetaPage;
use Illuminate\Http\Request;
use App\Models\HomeFeaturedProject;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function getHomePageData(Request $request)
    {

        if (Cache::has('home_page_data')) {
            return successResponse(Cache::get('home_page_data'), 'Home page data fetched successfully from cache.');
        }

        $homePageData = $this->getHomePageDataFromDatabase();
        //  Refresh cache (1 year) should be Queued in background later As New Feature
        Cache::put('home_page_data', $homePageData, now()->addYear());
        return successResponse($homePageData, 'Home page data fetched successfully from database.');
    }

    public function updateHomeHero(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status_text' => 'required|string',
            'status_active' => 'required|boolean',
            'full_name' => 'required|string',
            'role_title' => 'required|string',
            'headline' => 'required|string',
            'subheadline' => 'required|string',
        ]);
        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422, $validator->errors()->first());
        }

        $data = $validator->validated();

        $homeHero = HomeHero::updateOrCreate(
            ['id' => 1],
            $data
        );

        // Refresh cache - forget both single hero cache and full page data cache
        Cache::forget('home_hero');
        Cache::forget('home_page_data');

        return successResponse($homeHero, 'Home hero updated successfully.');
    }

    public function updateHomeFeaturedProjects(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'integer|nullable', // Optional for creating new projects
            'title' => 'required|string',
            'description' => 'required|string',
            'image_type' => 'required|in:emoji,url,icon',
            'image' => 'string|nullable', // Not required when image_type is icon
            'icon_key' => 'string|nullable', // required if image_type is icon
            'tech' => 'required|string',
            'sort_order' => 'required|integer',
        ]);

        // Add conditional validation rules
        $validator->sometimes('image', 'required|string', function ($input) {
            return in_array($input->image_type, ['emoji', 'url']);
        });

        $validator->sometimes('icon_key', 'required|string', function ($input) {
            return $input->image_type === 'icon';
        });

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422, $validator->errors()->first());
        }

        $data = $validator->validated();

        // When image_type is "icon", set image to null since icon_key is used instead
        if ($data['image_type'] === 'icon') {
            $data['image'] = null;
        }

        // Handle create vs update
        if (isset($data['id']) && $data['id'] !== null) {
            // Update existing project
            $homeFeaturedProject = HomeFeaturedProject::updateOrCreate(
                ['id' => $data['id']],
                $data
            );
        } else {
            // Create new project (remove id from data if present)
            unset($data['id']);
            $homeFeaturedProject = HomeFeaturedProject::create($data);
        }

        // Refresh cache - forget both single project cache and full page data cache
        Cache::forget('home_featured_projects');
        Cache::forget('home_page_data');
        return successResponse($homeFeaturedProject, 'Home featured project updated successfully.');
    }

    public function deleteHomeFeaturedProject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:home_featured_projects,id',
        ]);
        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422, $validator->errors()->first());
        }
        $data = $validator->validated();
        $homeFeaturedProject = HomeFeaturedProject::find($data['id']);
        $homeFeaturedProject->delete();

        // Refresh cache - forget both single project cache and full page data cache
        Cache::forget('home_featured_projects');
        Cache::forget('home_page_data');

        return successResponse(null, 'Home featured project deleted successfully.');
    }



    // ------------------------------ Private Functions ------------------------------
    // ------------------------------Private Functions ------------------------------
    // ------------------------------Private Functions ------------------------------



    public function getHomePageDataFromDatabase()
    {
        $homePageData = [
            'home_hero' => $this->getHomeHeroFromDatabase(),
            'home_featured_projects' => $this->getHomeFeaturedProjectsFromDatabase(),
        ];
        //  Refresh cache (1 year) should be Queued in background later As New Feature
        Cache::put('home_page_data', $homePageData, now()->addYear());
        return $homePageData;
    }

    private function getHomeHeroFromDatabase()
    {
        $homeHero = HomeHero::find(1);
        // Put in cache (1 year) should be Queued in background later As New Feature
        return $homeHero;
    }

    private function getHomeFeaturedProjectsFromDatabase()
    {
        $homeFeaturedProjects = HomeFeaturedProject::orderBy('sort_order', 'asc')->get();
        return $homeFeaturedProjects;
    }
}
