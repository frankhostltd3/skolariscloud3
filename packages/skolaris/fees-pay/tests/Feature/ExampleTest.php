<?php

namespace Skolaris\FeesPay\Tests\Feature;

use Skolaris\FeesPay\Tests\TestCase;

class ExampleTest extends TestCase
{
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function config_is_loaded()
    {
        $this->assertNotNull(config('fees-pay'));
    }
}
