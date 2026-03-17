<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleApiService
{
    /* ──────────────────────────────────────────────────────────────
     |  JWT helpers
     └────────────────────────────────────────────────────────────── */

    private function base64url(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function credentials(): ?array
    {
        $path = env('GOOGLE_SERVICE_ACCOUNT_JSON', storage_path('app/google-credentials.json'));

        if (! file_exists($path)) {
            return null;
        }

        $json = json_decode(file_get_contents($path), true);
        return (is_array($json) && isset($json['private_key'])) ? $json : null;
    }

    private function accessToken(string $scope): ?string
    {
        $cacheKey = 'google_token_' . md5($scope);

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $creds = $this->credentials();
        if (! $creds) {
            return null;
        }

        $now     = time();
        $header  = $this->base64url(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $payload = $this->base64url(json_encode([
            'iss'   => $creds['client_email'],
            'scope' => $scope,
            'aud'   => 'https://oauth2.googleapis.com/token',
            'exp'   => $now + 3600,
            'iat'   => $now,
        ]));

        $message   = $header . '.' . $payload;
        $signature = '';

        if (! openssl_sign($message, $signature, $creds['private_key'], 'sha256WithRSAEncryption')) {
            Log::warning('GoogleApiService: openssl_sign falló');
            return null;
        }

        $jwt = $message . '.' . $this->base64url($signature);

        try {
            $response = Http::timeout(10)->asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]);

            if ($response->failed()) {
                Log::warning('GoogleApiService: error al obtener token', ['body' => $response->body()]);
                return null;
            }

            $token     = $response->json('access_token');
            $expiresIn = (int) $response->json('expires_in', 3600);

            Cache::put($cacheKey, $token, now()->addSeconds($expiresIn - 60));

            return $token;
        } catch (\Throwable $e) {
            Log::warning('GoogleApiService: excepción al obtener token', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /* ──────────────────────────────────────────────────────────────
     |  GA4 Data API  (últimos 7 días)
     └────────────────────────────────────────────────────────────── */

    /**
     * @return array{active_users:int, sessions:int, page_views:int}|null
     */
    public function ga4Stats(string $propertyId): ?array
    {
        $cacheKey = 'ga4_stats_7d_' . $propertyId;

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $token = $this->accessToken('https://www.googleapis.com/auth/analytics.readonly');
        if (! $token) {
            return null;
        }

        try {
            $response = Http::timeout(15)
                ->withToken($token)
                ->post("https://analyticsdata.googleapis.com/v1beta/properties/{$propertyId}:runReport", [
                    'dateRanges' => [['startDate' => '7daysAgo', 'endDate' => 'today']],
                    'metrics'    => [
                        ['name' => 'activeUsers'],
                        ['name' => 'sessions'],
                        ['name' => 'screenPageViews'],
                    ],
                ]);

            if ($response->failed()) {
                Log::warning('GoogleApiService: GA4 error', ['status' => $response->status(), 'body' => $response->body()]);
                return null;
            }

            $values = $response->json('rows.0.metricValues') ?? [];

            $data = [
                'active_users' => (int) ($values[0]['value'] ?? 0),
                'sessions'     => (int) ($values[1]['value'] ?? 0),
                'page_views'   => (int) ($values[2]['value'] ?? 0),
            ];

            Cache::put($cacheKey, $data, now()->addHours(4));

            return $data;
        } catch (\Throwable $e) {
            Log::warning('GoogleApiService: excepción GA4', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /* ──────────────────────────────────────────────────────────────
     |  Search Console API  (últimos 7 días)
     └────────────────────────────────────────────────────────────── */

    /**
     * @return array{clicks:int, impressions:int, ctr:float, position:float}|null
     */
    public function searchConsoleStats(string $siteUrl): ?array
    {
        $cacheKey = 'sc_stats_7d_' . md5($siteUrl);

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $token = $this->accessToken('https://www.googleapis.com/auth/webmasters.readonly');
        if (! $token) {
            return null;
        }

        $endDate   = now()->subDay()->format('Y-m-d');
        $startDate = now()->subDays(8)->format('Y-m-d');
        $encoded   = rawurlencode(rtrim($siteUrl, '/') . '/');

        try {
            $response = Http::timeout(15)
                ->withToken($token)
                ->post("https://www.googleapis.com/webmasters/v3/sites/{$encoded}/searchAnalytics/query", [
                    'startDate'  => $startDate,
                    'endDate'    => $endDate,
                    'dimensions' => [],
                    'rowLimit'   => 1,
                ]);

            if ($response->failed()) {
                Log::warning('GoogleApiService: Search Console error', ['status' => $response->status(), 'body' => $response->body()]);
                return null;
            }

            $row = $response->json('rows.0') ?? [];

            $data = [
                'clicks'      => (int)   ($row['clicks']      ?? 0),
                'impressions' => (int)   ($row['impressions'] ?? 0),
                'ctr'         => round(  ($row['ctr']         ?? 0) * 100, 1),
                'position'    => round(   $row['position']    ?? 0, 1),
            ];

            Cache::put($cacheKey, $data, now()->addHours(4));

            return $data;
        } catch (\Throwable $e) {
            Log::warning('GoogleApiService: excepción Search Console', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /* ──────────────────────────────────────────────────────────────
     |  Verifica si la cuenta de servicio está configurada
     └────────────────────────────────────────────────────────────── */

    public function isConfigured(): bool
    {
        return $this->credentials() !== null;
    }
}
