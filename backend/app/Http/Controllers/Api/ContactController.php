<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ContactRequestReceived;
use App\Models\ContactRequest;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Contact / offerte form. Rate-limited and honeypot-guarded at the route.
 * Everything is validated server-side; the client is never trusted.
 */
class ContactController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:60'],
            'company' => ['nullable', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:5000'],
            'source' => ['nullable', 'string', 'max:255'],
            'consent' => ['accepted'],
        ]);

        unset($data['consent']);

        $contactRequest = ContactRequest::create($data);

        $to = Setting::get('contact_email') ?: config('mail.from.address');

        if ($to) {
            try {
                Mail::to($to)->send(new ContactRequestReceived($contactRequest));
            } catch (\Throwable $e) {
                // The submission is already saved; a mail outage must never lose it.
                Log::error('Failed to send contact request notification: '.$e->getMessage());
            }
        }

        return response()->json([
            'message' => 'Bedankt voor je bericht. We nemen zo snel mogelijk contact met je op.',
        ], 201);
    }
}
