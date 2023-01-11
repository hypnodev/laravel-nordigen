<?php

namespace Hypnodev\LaravelNordigen;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Traits\ForwardsCalls;
use Nordigen\NordigenPHP\API\NordigenClient;

class LaravelNordigen
{
    use ForwardsCalls;

    private const NORDIGEN_CACHE_TOKEN_KEY = 'nordigen-cache-token-key';
    private const NORDIGEN_CACHE_TIMESTAMP_KEY = 'nordigen-cache-timestamp-key';
    private const NORDIGEN_CACHE_REFRESH_TOKEN_KEY = 'nordigen-cache-refresh-token';

    private NordigenClient $client;

    public function __call(string $method, array $arguments): mixed
    {
        return $this->forwardCallTo(
            $this->nordigenClient(), $method, $arguments
        );
    }

    public function __construct()
    {
        $credentials = config('laravel-nordigen.credentials');

        $this->client = new NordigenClient($credentials['secret_id'], $credentials['secret_key']);
        $this->retrieveToken();
    }

    private function retrieveToken() : void
    {
        $token = Cache::get(self::NORDIGEN_CACHE_TOKEN_KEY);
        $requestedAt = Carbon::parse(Cache::get(self::NORDIGEN_CACHE_TIMESTAMP_KEY));

        if ($token && $requestedAt->diffInHours(now()) > 24) {
            $this->nordigenClient()->refreshAccessToken(self::NORDIGEN_CACHE_REFRESH_TOKEN_KEY);

            Cache::set(self::NORDIGEN_CACHE_TOKEN_KEY, $this->nordigenClient()->getAccessToken());
            Cache::set(self::NORDIGEN_CACHE_REFRESH_TOKEN_KEY, $this->nordigenClient()->getRefreshToken());
        }

        if ($token === null) {
            $this->nordigenClient()->createAccessToken();
            Cache::set(self::NORDIGEN_CACHE_TIMESTAMP_KEY, now());

            Cache::set(self::NORDIGEN_CACHE_TOKEN_KEY, $this->nordigenClient()->getAccessToken());
            Cache::set(self::NORDIGEN_CACHE_REFRESH_TOKEN_KEY, $this->nordigenClient()->getRefreshToken());
        }

        $this->nordigenClient()->setAccessToken(
            Cache::get(self::NORDIGEN_CACHE_TOKEN_KEY)
        );
        $this->nordigenClient()->setRefreshToken(
            Cache::get(self::NORDIGEN_CACHE_REFRESH_TOKEN_KEY)
        );
    }

    public function requestRequisitionForInstitution(string $institutionId, string $userId): string
    {
        $requisitionUri = config('laravel-nordigen.redirect.requisition_uri');
        $redirectUrl = url(
            "$requisitionUri?" . http_build_query(['user_id' => $userId])
        );

        $session = $this->nordigenClient()->initSession($institutionId, $redirectUrl);
        return $session['link'];
    }

    public function nordigenClient(): NordigenClient
    {
        return $this->client;
    }
}
