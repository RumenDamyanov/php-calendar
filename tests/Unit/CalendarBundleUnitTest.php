<?php
require_once __DIR__ . '/../Feature/SymfonyBundleStub.php';
/**
 * Unit test for Symfony CalendarBundle
 *
 * @package Rumenx\php-calendar
 */

use Rumenx\Symfony\CalendarBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;

describe('CalendarBundle', function () {
    it('extends Symfony Bundle', function () {
        $bundle = new CalendarBundle();
        expect($bundle)->toBeInstanceOf('Symfony\\Component\\HttpKernel\\Bundle\\Bundle');
    });
});
