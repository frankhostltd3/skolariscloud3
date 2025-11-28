<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * Minimal base case for central-only features (e.g., landlord area) that
 * should not run tenant initialization logic from Tests\TestCase.
 */
abstract class CentralTestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;
}
