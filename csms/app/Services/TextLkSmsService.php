<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\TextLkSmsService;

class TextLkSmsService
{
    public function send(
        string $phone,
        string $message
    ): bool {
        $recipient = $this->formatPhoneNumber($phone);

        if (! $recipient) {
            Log::error('Invalid Client phone number.', [
                'phone' => $phone,
            ]);

            return false;
        }

        $apiKey = config('services.textlk.api_key');
        $senderId = config('services.textlk.sender_id');

        if (! $apiKey || ! $senderId) {
            Log::error(
                'Text.lk API Key or Sender ID is missing.'
            );

            return false;
        }

        try {
            $response = Http::withToken($apiKey)
                ->withoutVerifying()
                ->acceptJson()
                ->timeout(30)
                ->post(
                    'https://app.text.lk/api/v3/sms/send',
                    [
                        'recipient' => $recipient,
                        'sender_id' => $senderId,
                        'type' => 'plain',
                        'message' => $message,
                    ]
                );

            Log::info('Text.lk API response.', [
                'recipient' => $recipient,
                'status_code' => $response->status(),
                'response' => $response->body(),
            ]);

           
            return $response->successful();

        } catch (\Throwable $exception) {
            Log::error('Text.lk connection error.', [
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    private function formatPhoneNumber(
        string $phone
    ): ?string {
        $number = preg_replace(
            '/[^0-9]/',
            '',
            $phone
        );

      
        if (
            str_starts_with($number, '0') &&
            strlen($number) === 10
        ) {
            return '94' . substr($number, 1);
        }

        
        if (
            str_starts_with($number, '94') &&
            strlen($number) === 11
        ) {
            return $number;
        }

        return null;
    }
}