<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrevoMailer
{
    public function sendVerificationCode(User $user, string $code): bool
    {
        $apiKey = config('services.brevo.api_key');

        if (!$apiKey) {
            Log::error('Brevo API key is missing.');
            return false;
        }

        $subject = 'رمز التحقق من البريد الإلكتروني - OrderFlow';

        $text = "رمز التحقق الخاص بك في OrderFlow هو: {$code}\n\n"
            . 'الرمز صالح لمدة 10 دقائق.';

        try {
            $response = Http::acceptJson()
                ->withHeaders(['api-key' => $apiKey])
                ->connectTimeout(5)
                ->timeout(15)
                ->post('https://api.brevo.com/v3/smtp/email', [
                    'sender' => [
                        'name' => config('mail.from.name'),
                        'email' => config('mail.from.address'),
                    ],
                    'to' => [
                        ['email' => $user->email],
                    ],
                    'subject' => $subject,
                    'textContent' => $text,
                ]);

            if (!$response->successful()) {
                Log::error('Brevo API email request failed.', [
                    'status' => $response->status(),
                ]);

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('Brevo API email request failed.', [
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
