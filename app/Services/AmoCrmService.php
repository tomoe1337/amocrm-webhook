<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AmoCrmService
{
    private string $clientId;
    private string $clientSecret;
    private string $subdomain;
    private ?string $accessToken;

    /**
     * Конструктор сервиса.
     *
     * @param string $clientId
     * @param string $clientSecret
     * @param string $subdomain
     * @param string|null $accessToken
     */
    public function __construct(
        string  $clientId,
        string  $clientSecret,
        string  $subdomain,
        ?string $accessToken = null
    )
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->subdomain = $subdomain;
        $this->accessToken = $accessToken;
    }

    /**
     * Получение токенов с помощью Authorization Code.
     *
     * @param string $authorizationCode
     * @return array|null
     */
    public function getTokens(string $authorizationCode): ?array
    {
        $response = Http::withOptions(['verify' => false])->post("https://{$this->subdomain}.amocrm.ru/oauth2/access_token", [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'authorization_code',
            'code' => $authorizationCode,
            'redirect_uri' => config('services.amocrm.redirect_uri'),
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Failed to get tokens:', ['response' => $response->body()]);
        return null;
    }

    /**
     * Обновление токенов с помощью Refresh Token.
     *
     * @param string $refreshToken
     * @return array|null
     */
    public function refreshAccessToken(string $refreshToken): ?array
    {
        $response = Http::withOptions(['verify' => false])->post("https://{$this->subdomain}.amocrm.ru/oauth2/access_token", [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Failed to refresh tokens:', ['response' => $response->body()]);
        return null;
    }

    /**
     * Добавление примечания через API amoCRM.
     *
     * @param string $entityType
     * @param int $entityId
     * @param string $noteText
     * @return bool
     */
    public function addNote(string $entityType, int $entityId, string $noteText): bool
    {
        try {

            $url = "https://{$this->subdomain}.amocrm.ru/api/v4/{$entityType}s/notes";


            $payload = [
                [
                    'entity_id' => $entityId,
                    'note_type' => 'common',
                    'params' => [
                        'text' => $noteText,
                    ],
                ],
            ];

            $response = Http::withOptions(['verify' => false])
                ->withHeaders([
                    'Authorization' => "Bearer {$this->accessToken}",
                    'Content-Type' => 'application/json',
                ])
                ->post($url, $payload);

            if ($response->successful()) {
                return true;
            }

            Log::error('Failed to add note:', ['response' => $response->body()]);
            return false;
        } catch (\Exception $e) {
            Log::error('Error adding note:', ['message' => $e->getMessage()]);
            return false;
        }
    }
}
