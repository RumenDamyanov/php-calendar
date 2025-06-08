<?php
/**
 * Feature test for Laravel integration
 *
 * @package Rumenx\php-calendar
 */

require_once __DIR__ . '/LaravelServiceProviderStub.php';

describe('Laravel CalendarServiceProvider', function () {
    it('registers the Calendar singleton in a Laravel-like container', function () {
        // Simulate a minimal Laravel container
        $app = new class {
            public array $bindings = [];
            public function singleton($class, $closure) {
                $this->bindings[$class] = $closure();
            }
        };
        $provider = new \Rumenx\Laravel\CalendarServiceProvider($app);
        $provider->register();
        expect($app->bindings)->toHaveKey(\Rumenx\Calendar::class);
        expect($app->bindings[\Rumenx\Calendar::class])->toBeInstanceOf(\Rumenx\Calendar::class);
    });
});
