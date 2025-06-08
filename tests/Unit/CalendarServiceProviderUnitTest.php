<?php
/**
 * Unit test for CalendarServiceProvider logic
 *
 * @package Rumenx\php-calendar
 */

require_once __DIR__ . '/../Feature/LaravelServiceProviderStub.php';

use Rumenx\Laravel\CalendarServiceProvider;
use Rumenx\Calendar;

describe('CalendarServiceProvider', function () {
    it('registers Calendar as singleton', function () {
        $app = new class {
            public array $singletons = [];
            public function singleton($class, $closure) {
                $this->singletons[$class] = $closure();
            }
        };
        $provider = new CalendarServiceProvider($app);
        $provider->register();
        expect($app->singletons)->toHaveKey(Calendar::class);
        expect($app->singletons[Calendar::class])->toBeInstanceOf(Calendar::class);
    });
});
