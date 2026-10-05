<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackService
{
    protected ?string $secretKey;

    protected ?string $publicKey;

    protected string $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret_key') ?: env('PAYSTACK_SECRET_KEY');
        $this->publicKey = config('services.paystack.public_key') ?: env('PAYSTACK_PUBLIC_KEY');
        $this->baseUrl = rtrim((string) (config('services.paystack.payment_url') ?: 'https://api.paystack.co'), '/');
    }

    public function isConfigured(): bool
    {
        return ! empty($this->secretKey);
    }

    public function getPublicKey(): ?string
    {
        return $this->publicKey;
    }

    /**
     * Initialize a Paystack transaction.
     *
     * @param array{
     *     email: string,
     *     amount: int, // in kobo (NGN * 100)
     *     reference: string,
     *     callback_url: string,
     *     metadata?: array,
     *     channels?: array
     * } $data
     * @return array
     * @throws Exception
     */
    public function initializeTransaction(array $data): array
    {
        if (! $this->isConfigured()) {
            throw new Exception('Paystack is not configured. Please supply PAYSTACK_SECRET_KEY in your environment.');
        }

        $response = Http::withToken($this->secretKey)
            ->acceptJson()
            ->timeout(20)
            ->post("{$this->baseUrl}/transaction/initialize", [
                'email' => $data['email'],
                'amount' => $data['amount'],
                'reference' => $data['reference'],
                'callback_url' => $data['callback_url'],
                'metadata' => $data['metadata'] ?? [],
                'channels' => $data['channels'] ?? ['card', 'bank', 'ussd', 'qr', 'mobile_money', 'bank_transfer', 'eft'],
            ]);

        if (! $response->successful()) {
            Log::error('Paystack transaction initialization failed', [
                'status' => $response->status(),
                'body' => $response->json() ?? $response->body(),
                'reference' => $data['reference'] ?? null,
            ]);

            $errorMsg = $response->json('message') ?? 'Unable to initialize Paystack transaction.';
            throw new Exception($errorMsg);
        }

        return $response->json();
    }

    /**
     * Verify a transaction via reference.
     */
    public function verifyTransaction(string $reference): array
    {
        if (! $this->isConfigured()) {
            throw new Exception('Paystack is not configured.');
        }

        $response = Http::withToken($this->secretKey)
            ->acceptJson()
            ->timeout(20)
            ->get("{$this->baseUrl}/transaction/verify/{$reference}");

        if (! $response->successful()) {
            Log::error('Paystack verification request failed', [
                'status' => $response->status(),
                'body' => $response->json() ?? $response->body(),
                'reference' => $reference,
            ]);

            return [
                'status' => false,
                'message' => $response->json('message') ?? 'Verification failed',
            ];
        }

        return $response->json();
    }

    /**
     * Validate Paystack webhook HMAC signature.
     */
    public function verifyWebhookSignature(string $rawContent, ?string $signature): bool
    {
        if (empty($this->secretKey) || empty($signature)) {
            return false;
        }

        $computed = hash_hmac('sha512', $rawContent, $this->secretKey);

        return hash_equals($computed, $signature);
    }
}
