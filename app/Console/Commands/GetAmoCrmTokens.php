<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GetAmoCrmTokens extends Command
{
    protected $signature = 'amocrm:get-tokens {code}';
    protected $description = 'Получение токенов из amoCRM с использованием Authorization Code';

    public function handle(): void
    {
        $authorizationCode = $this->argument('code');

        if (empty($authorizationCode)) {
            $this->error('Authorization Code не указан.');
            return;
        }

        $clientId = config('services.amocrm.client_id');
        $clientSecret = config('services.amocrm.client_secret');
        $subdomain = config('services.amocrm.subdomain');
        $redirectUri = config('services.amocrm.redirect_uri');

        ##dd($redirectUri);
        try {
            $response = Http::withOptions(['verify' => false])->post("https://{$subdomain}.amocrm.ru/oauth2/access_token", [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'grant_type' => 'authorization_code',
                'code' => $authorizationCode,
                'redirect_uri' => $redirectUri,
            ]);

            if ($response->successful()) {
                $tokens = $response->json();

                $this->info('Токены успешно получены:');
                $this->line("Access Token: {$tokens['access_token']}");
                $this->line("Refresh Token: {$tokens['refresh_token']}");

                $this->saveTokensToEnv($tokens);
            } else {
                $this->error('Не удалось получить токены.');
                Log::error('Failed to get tokens:', ['response' => $response->body()]);
            }
        } catch (\Exception $e) {
            $this->error('Ошибка при выполнении запроса.');
            Log::error('Error getting tokens:', ['message' => $e->getMessage()]);
        }
    }

    private function saveTokensToEnv(array $tokens): void
    {
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        $envContent = preg_replace(
            '/AMOCRM_ACCESS_TOKEN=.*/',
            "AMOCRM_ACCESS_TOKEN={$tokens['access_token']}",
            $envContent
        );
        $envContent = preg_replace(
            '/AMOCRM_REFRESH_TOKEN=.*/',
            "AMOCRM_REFRESH_TOKEN={$tokens['refresh_token']}",
            $envContent
        );

        file_put_contents($envPath, $envContent);

        $this->info('Токены успешно сохранены в .env файл.');
    }
}
