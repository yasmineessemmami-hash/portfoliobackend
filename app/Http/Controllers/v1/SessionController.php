<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\AppKey;
use App\Models\UserKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SessionController extends Controller
{
    /**
     * Initialize session - validate/generate u and a keys
     * 
     * Priority: URL params > localStorage > backend-generated
     * Backend validates all keys and is the single source of truth
     */
    public function init(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'u' => 'nullable|string|max:64',
            'a' => 'nullable|string|max:64',
        ]);

        if ($validator->fails()) {
            return errorResponse('Invalid parameters', 422);
        }

        $ipAddress = $request->ip();

        // Get u key from request or generate new one
        $uKey = $request->input('u');
        if (!$uKey) {
            $uKey = UserKey::generateKey();
        }

        // Get a key from request or set to 'unknown'
        $aKey = $request->input('a');
        if (!$aKey) {
            $aKey = AppKey::generateKey();
        }

        // Validate and find or create keys (backend is source of truth)
        $userKey = UserKey::findOrCreate($uKey, $ipAddress);
        $appKey = AppKey::findOrCreate($aKey);

        return successResponse([
            'u' => $userKey->key,
            'a' => $appKey->key,
        ], 'Session initialized');
    }

}
