<?php

namespace Ttechnos\Telebirr\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ttechnos\Telebirr\Facades\Telebirr;
use Illuminate\Support\Facades\Log;

class TelebirrController extends Controller
{
    /**
     * Handle Telebirr payment callback/webhook
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function callback(Request $request)
    {
        try {
            $data = $request->all();

            // Log the callback data
            Log::info('Telebirr Callback Received', $data);

            // Verify signature
            if (!Telebirr::verifySignature($data)) {
                Log::error('Telebirr Callback: Invalid signature', $data);
                return response()->json(['msg' => 'Invalid signature'], 400);
            }

            // Fire event for the application to handle
            event(new \Ttechnos\Telebirr\Events\TelebirrPaymentReceived($data));

            return response()->json([
                'msg' => 'success',
                'code' => 0
            ]);
        } catch (\Exception $e) {
            Log::error('Telebirr Callback Error: ' . $e->getMessage(), [
                'exception' => $e,
                'data' => $request->all()
            ]);

            return response()->json([
                'msg' => 'error',
                'code' => -1
            ], 500);
        }
    }
}