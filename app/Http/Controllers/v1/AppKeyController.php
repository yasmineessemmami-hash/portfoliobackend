<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\AppKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppKeyController extends Controller
{
    /**
     * Store or update app key
     * Prevents duplicates using firstOrCreate
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'appKey' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return errorResponse('Invalid parameters', 422);
        }

        $appKey = $request->input('appKey');
        
        // Validate key format - must be exactly 4 uppercase letters
        if (!preg_match('/^[A-Z]{4}$/', $appKey)) {
            return errorResponse('Invalid key format. Key must be exactly 4 uppercase letters.', 422);
        }

        // Check if key already exists
        $existingKey = AppKey::where('key', $appKey)->first();
        if ($existingKey) {
            // Key already exists, return it without creating duplicate
            return successResponse([
                'appKey' => $existingKey->key,
                'id' => $existingKey->id,
            ], 'App key already exists');
        }

        // Create new key
        $key = AppKey::create([
            'key' => $appKey,
            'source' => 'unknown',
        ]);

        return successResponse([
            'appKey' => $key->key,
            'id' => $key->id,
        ], 'App key stored successfully');
    }

    /**
     * List all app keys (admin only)
     */
    public function index(Request $request)
    {
        $limit = min((int)$request->input('limit', 50), 100);
        $page = max((int)$request->input('page', 1), 1);
        $search = $request->input('search');

        $query = AppKey::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                  ->orWhere('source', 'like', "%{$search}%");
            });
        }

        $keys = $query->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        return successResponse([
            'data' => $keys->items(),
            'current_page' => $keys->currentPage(),
            'last_page' => $keys->lastPage(),
            'per_page' => $keys->perPage(),
            'total' => $keys->total(),
        ], 'App keys retrieved');
    }

    /**
     * Update app key source
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'source' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return errorResponse('Invalid parameters', 422);
        }

        $key = AppKey::find($id);
        if (!$key) {
            return errorResponse('App key not found', 404);
        }

        $key->source = $request->input('source');
        $key->save();

        return successResponse($key, 'App key updated successfully');
    }

    /**
     * Delete app key
     */
    public function destroy($id)
    {
        $key = AppKey::find($id);
        if (!$key) {
            return errorResponse('App key not found', 404);
        }

        // Prevent deleting the default UNKN key
        if ($key->key === 'UNKN') {
            return errorResponse('Cannot delete the default unknown app key', 400);
        }

        $key->delete();

        return successResponse(null, 'App key deleted successfully');
    }
}
