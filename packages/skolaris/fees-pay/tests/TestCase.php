<?php

namespace Skolaris\FeesPay\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Skolaris\FeesPay\SkolarisFeesPayServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            SkolarisFeesPayServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}
