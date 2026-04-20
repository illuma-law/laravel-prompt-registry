<?php

declare(strict_types=1);

namespace IllumaLaw\PromptRegistry;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PromptRegistryServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-prompt-registry')
            ->hasConfigFile('prompt-registry');
    }

    public function registeringPackage(): void
    {
        $this->app->singleton('prompt-registry', function (): PromptRegistryManager {
            return new PromptRegistryManager;
        });
    }

    public function bootingPackage(): void
    {
        /** @var PromptRegistryManager $manager */
        $manager = $this->app->make('prompt-registry');

        /** @var array<string, array{agent: class-string, name: string, description: string, fallback_view: string, default_primary_tier?: 'standard'|'extended'}> $prompts */
        $prompts = config('prompt-registry.prompts', []);

        if ($prompts !== []) {
            $manager->registerMany($prompts);
        }
    }
}
