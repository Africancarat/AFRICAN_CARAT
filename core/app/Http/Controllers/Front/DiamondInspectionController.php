<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Stub for realtime gemologist inspection (Retell AI + Twilio + screen-share).
 * Wire this route to external booking / voice agent when infra is configured.
 */
class DiamondInspectionController extends Controller
{
    public function requestSession(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'item_id' => 'required|integer|exists:items,id',
            'contact_email' => 'nullable|email|max:255',
            'note' => 'nullable|string|max:2000',
        ]);

        Log::info('diamond.inspection.stub', [
            'item_id' => $validated['item_id'],
            'email' => $validated['contact_email'] ?? null,
            'note' => $validated['note'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Thanks — our team will coordinate a guided diamond review shortly.')
                . ' '
                . __('Connect Retell AI + Twilio in DiamondInspectionController when your stack is ready.'),
        ]);
    }
}
