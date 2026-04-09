<?php

namespace App\Services\Paymongo;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PaymongoClient
{
    protected string $baseUrl;

    protected string $secretKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.paymongo.base_url', 'https://api.paymongo.com/v1'), '/');
        $this->secretKey = (string) config('services.paymongo.secret_key');
    }

    public function isConfigured(): bool
    {
        return $this->secretKey !== '';
    }

    public function createPaymentIntent(int $amountInCentavos, string $description, array $metadata = []): array
    {
        return $this->request()->post('/payment_intents', [
            'data' => [
                'attributes' => [
                    'amount' => $amountInCentavos,
                    'currency' => 'PHP',
                    'capture_type' => 'automatic',
                    'description' => $description,
                    'payment_method_allowed' => ['qrph', 'gcash', 'paymaya'],
                    'metadata' => $metadata,
                ],
            ],
        ])->throw()->json();
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function createPaymentMethod(
        string $type,
        string $name,
        string $email,
        array $metadata = [],
        ?string $successUrl = null,
        ?string $failedUrl = null
    ): array {
        $attributes = [
            'type' => $type,
            'billing' => [
                'name' => $name,
                'email' => $email,
            ],
            'metadata' => $metadata,
        ];

        if (in_array($type, ['gcash', 'paymaya'], true)) {
            $attributes['details'] = [
                'redirect' => [
                    'success' => $successUrl,
                    'failed' => $failedUrl,
                ],
            ];
        }

        return $this->request()->post('/payment_methods', [
            'data' => [
                'attributes' => $attributes,
            ],
        ])->throw()->json();
    }

    public function attachPaymentIntent(string $paymentIntentId, string $paymentMethodId, string $returnUrl): array
    {
        return $this->request()->post("/payment_intents/{$paymentIntentId}/attach", [
            'data' => [
                'attributes' => [
                    'payment_method' => $paymentMethodId,
                    'return_url' => $returnUrl,
                ],
            ],
        ])->throw()->json();
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function retrievePaymentIntent(string $paymentIntentId): array
    {
        return $this->request()->get("/payment_intents/{$paymentIntentId}")
            ->throw()
            ->json();
    }

    protected function request(): PendingRequest
    {
        $this->ensureConfigured();

        return Http::baseUrl($this->baseUrl)
            ->withBasicAuth($this->secretKey, '')
            ->acceptJson()
            ->asJson()
            ->timeout(20);
    }

    protected function ensureConfigured(): void
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException('PAYMONGO_SECRET_KEY is not configured.');
        }
    }
}
