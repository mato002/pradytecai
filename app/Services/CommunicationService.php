<?php

namespace App\Services;

use App\Mail\ApplicationReplyMail;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CommunicationService
{
    /**
     * Send email to applicant
     */
    public function sendEmail(JobApplication $application, string $message, ?string $subject = null): bool
    {
        try {
            Mail::to($application->email)
                ->send(new ApplicationReplyMail($application, $message, $subject));
            
            Log::info('Email sent to applicant', [
                'application_id' => $application->id,
                'email' => $application->email,
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send email to applicant', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Send SMS to applicant using BulkSMS CRM API
     * Documentation: https://crm.pradytecai.com/api-documentation
     */
    public function sendSMS(JobApplication $application, string $message): bool
    {
        if (!$application->phone) {
            Log::warning('Cannot send SMS: No phone number provided', [
                'application_id' => $application->id,
            ]);
            return false;
        }

        $apiKey = config('services.bulksms.api_key');
        $clientId = config('services.bulksms.client_id', '1');
        $senderId = config('services.bulksms.sender_id');
        $apiUrl = config('services.bulksms.api_url', 'https://crm.pradytecai.com/api');

        if (!$apiKey || !$senderId) {
            Log::error('BulkSMS CRM API credentials not configured', [
                'application_id' => $application->id,
            ]);
            return false;
        }

        try {
            // Format phone number to international format (remove leading + if present)
            $phone = preg_replace('/^\+/', '', $application->phone);
            // Ensure it starts with country code (254 for Kenya)
            if (!str_starts_with($phone, '254')) {
                // If starts with 0, replace with 254
                if (str_starts_with($phone, '0')) {
                    $phone = '254' . substr($phone, 1);
                } else {
                    $phone = '254' . $phone;
                }
            }

            $response = Http::withHeaders([
                'X-API-KEY' => $apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$apiUrl}/{$clientId}/messages/send", [
                'client_id' => (int) $clientId,
                'channel' => 'sms',
                'recipient' => $phone,
                'sender' => $senderId,
                'body' => $message,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('SMS sent successfully via BulkSMS CRM', [
                    'application_id' => $application->id,
                    'phone' => $phone,
                    'message_id' => $result['data']['id'] ?? null,
                    'status' => $result['data']['status'] ?? null,
                ]);
                return true;
            } else {
                Log::error('BulkSMS CRM API error', [
                    'application_id' => $application->id,
                    'phone' => $phone,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Failed to send SMS to applicant', [
                'application_id' => $application->id,
                'phone' => $application->phone,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Send WhatsApp message to applicant using Ultramsg API
     */
    public function sendWhatsApp(JobApplication $application, string $message): bool
    {
        if (!$application->phone) {
            Log::warning('Cannot send WhatsApp: No phone number provided', [
                'application_id' => $application->id,
            ]);
            return false;
        }

        $instanceId = config('services.ultramsg.instance_id');
        $token = config('services.ultramsg.token');
        $apiUrl = config('services.ultramsg.api_url', 'https://api.ultramsg.com');

        if (!$instanceId || !$token) {
            Log::error('Ultramsg API credentials not configured', [
                'application_id' => $application->id,
            ]);
            return false;
        }

        try {
            // Format phone number for Ultramsg (international format without +)
            $phone = preg_replace('/^\+/', '', $application->phone);
            // Ensure it starts with country code (254 for Kenya)
            if (!str_starts_with($phone, '254')) {
                // If starts with 0, replace with 254
                if (str_starts_with($phone, '0')) {
                    $phone = '254' . substr($phone, 1);
                } else {
                    $phone = '254' . $phone;
                }
            }

            // Ultramsg API endpoint: POST /{instance_id}/messages/chat
            $response = Http::post("{$apiUrl}/{$instanceId}/messages/chat", [
                'token' => $token,
                'to' => $phone,
                'body' => $message,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('WhatsApp message sent successfully via Ultramsg', [
                    'application_id' => $application->id,
                    'phone' => $phone,
                    'message_id' => $result['id'] ?? null,
                ]);
                return true;
            } else {
                Log::error('Ultramsg API error', [
                    'application_id' => $application->id,
                    'phone' => $phone,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Failed to send WhatsApp to applicant', [
                'application_id' => $application->id,
                'phone' => $application->phone,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Send message via multiple channels
     */
    public function sendMultiple(JobApplication $application, array $channels, string $message, ?string $subject = null): array
    {
        $results = [];

        foreach ($channels as $channel) {
            switch ($channel) {
                case 'email':
                    $results['email'] = $this->sendEmail($application, $message, $subject);
                    break;
                case 'sms':
                    $results['sms'] = $this->sendSMS($application, $message);
                    break;
                case 'whatsapp':
                    $results['whatsapp'] = $this->sendWhatsApp($application, $message);
                    break;
            }
        }

        return $results;
    }
}

