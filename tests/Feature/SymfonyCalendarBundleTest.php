<?php
/**
 * Feature test for Symfony integration
 *
 * @package Rumenx\php-calendar
 */

require_once __DIR__ . '/SymfonyBundleStub.php';

use Symfony\Component\HttpKernel\Bundle\Bundle;

describe('Symfony CalendarBundle', function () {
    it('can be instantiated and is a Bundle', function () {
        $bundle = new \Rumenx\Symfony\CalendarBundle();
        expect($bundle)->toBeInstanceOf('Symfony\\Component\\HttpKernel\\Bundle\\Bundle');
    });
});
