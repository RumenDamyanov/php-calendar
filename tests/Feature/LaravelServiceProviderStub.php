<?php

namespace Illuminate\Support {

    if (!class_exists('Illuminate\\Support\\ServiceProvider')) {
        abstract class ServiceProvider {
            public $app;
            public function __construct($app = null) { $this->app = $app; }
        }
    }
}
