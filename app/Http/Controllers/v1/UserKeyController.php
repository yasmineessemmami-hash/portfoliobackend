<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\UserKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserKeyController extends Controller
{
    /**
     * Store or update user key
     * Prevents duplicates using firstOrCreate
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userKey' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return errorResponse('Invalid parameters', 422);
        }

        $userKey = $request->input('userKey');
        
        // Validate key format - must be exactly 4 uppercase letters
        if (!preg_match('/^[A-Z]{4}$/', $userKey)) {
            return errorResponse('Invalid key format. Key must be exactly 4 uppercase letters.', 422);
        }

        // Check if key already exists
        $existingKey = UserKey::where('key', $userKey)->first();
        if ($existingKey) {
            // Key already exists, return it without creating duplicate
            return successResponse([
                'userKey' => $existingKey->key,
                'id' => $existingKey->id,
            ], 'User key already exists');
        }

        $ipAddress = $request->ip();

        // Create new key
        $key = UserKey::create([
            'key' => $userKey,
            'label' => 'unknown',
            'ip_address' => $ipAddress,
        ]);

        return successResponse([
            'userKey' => $key->key,
            'id' => $key->id,
        ], 'User key stored successfully');
    }

    /**
     * List all user keys (admin only)
     */
    public function index(Request $request)
    {
        $limit = min((int)$request->input('limit', 50), 100);
        $page = max((int)$request->input('page', 1), 1);
        $search = $request->input('search');

        $query = UserKey::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                  ->orWhere('label', 'like', "%{$search}%");
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
        ], 'User keys retrieved');
    }

    /**
     * Update user key label
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return errorResponse('Invalid parameters', 422);
        }

        $key = UserKey::find($id);
        if (!$key) {
            return errorResponse('User key not found', 404);
        }

        $key->label = $request->input('label');
        $key->save();

        return successResponse($key, 'User key updated successfully');
    }

    /**
     * Delete user key
     */
    public function destroy($id)
    {
        $key = UserKey::find($id);
        if (!$key) {
            return errorResponse('User key not found', 404);
        }

        $key->delete();

        return successResponse(null, 'User key deleted successfully');
    }
}
