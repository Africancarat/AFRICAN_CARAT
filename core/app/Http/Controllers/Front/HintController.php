<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Mail\HintEmail;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class HintController extends Controller
{
    public function send(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'item_id' => 'required|integer|exists:items,id',
            'recipient_name' => 'required|string|max:100',
            'recipient_email' => 'required|email|max:255',
            'occasion' => 'required|string|max:80',
            'message' => 'nullable|string|max:500',
        ]);

        /** @var \App\Models\Item $product */
        $product = Item::where('id', $data['item_id'])->where('status', 1)->firstOrFail();

        try {
            Mail::to($data['recipient_email'])
                ->send(new HintEmail($product, $data));
        } catch (\Throwable $e) {
            Log::warning('hint.mail.failed', ['e' => $e->getMessage()]);

            return back()->with('error', __('We could not send the email yet. Try again shortly or email us.'));
        }

        return back()->with('success', __('Your hint has been sent!'));
    }
}
