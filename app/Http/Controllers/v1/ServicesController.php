<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\ServicesHero;
use App\Models\ServiceItem;
use App\Models\WhyChooseMeItem;
use App\Models\DeliverableItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ServicesController extends Controller
{
    public function getServicesData()
    {
        if (Cache::has('services_page_data')) {
            return successResponse(Cache::get('services_page_data'), 'Services data fetched successfully from cache.');
        }

        $servicesData = $this->getServicesDataFromDatabase();
        Cache::put('services_page_data', $servicesData, now()->addYear());
        return successResponse($servicesData, 'Services data fetched successfully from database.');
    }

    public function updateServicesHero(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'subtitle' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422);
        }

        $hero = ServicesHero::updateOrCreate(['id' => 1], $validator->validated());
        Cache::forget('services_page_data');
        return successResponse($hero, 'Services hero updated successfully');
    }

    public function updateServicesItems(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'services' => 'required|array',
            'services.*.title' => 'required|string',
            'services.*.description' => 'required|string',
            'services.*.icon_key' => 'required|string',
            'services.*.color' => 'nullable|string',
            'services.*.features' => 'required|array',
            'services.*.features.*' => 'string',
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422);
        }

        $data = $validator->validated();

        try {
            DB::beginTransaction();
            ServiceItem::query()->delete();
            foreach ($data['services'] as $index => $item) {
                ServiceItem::create(array_merge($item, ['sort_order' => $index]));
            }
            DB::commit();
            Cache::forget('services_page_data');
            return successResponse(null, 'Service items updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return errorResponse('Failed to update service items');
        }
    }

    public function updateWhyChooseMe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'why_choose_me' => 'required|array',
            'why_choose_me.*.title' => 'required|string',
            'why_choose_me.*.description' => 'required|string',
            'why_choose_me.*.icon_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422);
        }

        $data = $validator->validated();

        try {
            DB::beginTransaction();
            WhyChooseMeItem::query()->delete();
            foreach ($data['why_choose_me'] as $index => $item) {
                WhyChooseMeItem::create(array_merge($item, ['sort_order' => $index]));
            }
            DB::commit();
            Cache::forget('services_page_data');
            return successResponse(null, 'Why choose me updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return errorResponse('Failed to update why choose me items');
        }
    }

    public function updateDeliverables(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'deliverables' => 'required|array',
            'deliverables.*.title' => 'required|string',
            'deliverables.*.description' => 'required|string',
            'deliverables.*.icon_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return errorResponse($validator->errors()->first(), 422);
        }

        $data = $validator->validated();

        try {
            DB::beginTransaction();
            DeliverableItem::query()->delete();
            foreach ($data['deliverables'] as $index => $item) {
                DeliverableItem::create(array_merge($item, ['sort_order' => $index]));
            }
            DB::commit();
            Cache::forget('services_page_data');
            return successResponse(null, 'Deliverables updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return errorResponse('Failed to update deliverable items');
        }
    }

    public function getServicesDataFromDatabase()
    {
        $hero = ServicesHero::first();
        $services = ServiceItem::orderBy('sort_order')->get();
        $whyChooseMe = WhyChooseMeItem::orderBy('sort_order')->get();
        $deliverables = DeliverableItem::orderBy('sort_order')->get();

        return [
            'hero' => $hero,
            'services' => $services,
            'why_choose_me' => $whyChooseMe,
            'deliverables' => $deliverables,
        ];
    }
}
