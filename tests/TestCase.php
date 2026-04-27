<?php

declare(strict_types=1);

namespace IllumaLaw\PromptRegistry\Tests;

use IllumaLaw\PromptRegistry\PromptRegistryServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            PromptRegistryServiceProvider::class,
        ];
    }
}
