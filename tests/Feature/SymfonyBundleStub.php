<?php
// Polyfill for Symfony\Component\HttpKernel\Bundle\Bundle for testing
namespace Symfony\Component\HttpKernel\Bundle {
    if (!class_exists('Symfony\\Component\\HttpKernel\\Bundle\\Bundle')) {
        abstract class Bundle {}
    }
}
