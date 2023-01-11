<?php

namespace Hypnodev\LaravelNordigen\Facades;

use Illuminate\Support\Facades\Facade;
use Nordigen\NordigenPHP\API\NordigenClient;

/**
 * @method static string requestRequisitionForInstitution(string $institutionId, string $userId)
 * @method static NordigenClient nordigenClient()
 *
 * @mixin NordigenClient
 * @see \Hypnodev\LaravelNordigen\LaravelNordigen
 */
class LaravelNordigen extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-nordigen';
    }
}
