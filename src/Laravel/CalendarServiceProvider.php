<?php
/**
 * Laravel Calendar Service Provider
 *
 * Provides integration for the php-calendar package with Laravel.
 *
 * @package Rumenx\php-calendar
 */

namespace Rumenx\Laravel;

use Illuminate\Support\ServiceProvider;
use Rumenx\Calendar;

class CalendarServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Calendar::class, function () {
            return new Calendar();
        });
    }
}
