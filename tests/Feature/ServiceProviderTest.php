<?php

declare(strict_types=1);

use IllumaLaw\PromptRegistry\AgentKeyResolver;
use IllumaLaw\PromptRegistry\Contracts\PromptContentStore;
use IllumaLaw\PromptRegistry\EloquentPromptContentStore;
use IllumaLaw\PromptRegistry\NullPromptContentStore;
use IllumaLaw\PromptRegistry\PromptBodyResolver;
use IllumaLaw\PromptRegistry\PromptPersistenceManager;
use IllumaLaw\PromptRegistry\PromptRegistryManager;
use IllumaLaw\PromptRegistry\PromptRegistryServiceProvider;
use IllumaLaw\PromptRegistry\PromptTemplateRenderer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;

it('auto-registers config-defined prompts on boot via the service provider', function (): void {
    config(['prompt-registry.prompts' => [
        'agents.sp_driven' => [
            'agent' => 'SPAgent',
            'name' => 'SP Agent',
            'description' => 'Loaded via SP boot',
            'fallback_view' => 'prompts.sp',
        ],
    ]]);

    /** @var Application $app */
    $app = app();
    $app->forgetInstance('prompt-registry');

    /** @var PromptRegistryServiceProvider $sp */
    $sp = new PromptRegistryServiceProvider($app);
    $sp->registeringPackage();
    $sp->bootingPackage();

    /** @var PromptRegistryManager $manager */
    $manager = $app->make('prompt-registry');

    expect($manager->forKey('agents.sp_driven')['agent'])->toBe('SPAgent');
});

it('registers prompt infrastructure services in the container', function (): void {
    /** @var Application $app */
    $app = app();
    $app->forgetInstance('prompt-registry');

    /** @var PromptRegistryServiceProvider $sp */
    $sp = new PromptRegistryServiceProvider($app);
    $sp->registeringPackage();
    $sp->bootingPackage();

    expect($app->make(PromptRegistryManager::class))->toBeInstanceOf(PromptRegistryManager::class)
        ->and($app->make(PromptTemplateRenderer::class))->toBeInstanceOf(PromptTemplateRenderer::class)
        ->and($app->make(PromptBodyResolver::class))->toBeInstanceOf(PromptBodyResolver::class)
        ->and($app->make(AgentKeyResolver::class))->toBeInstanceOf(AgentKeyResolver::class)
        ->and($app->make(PromptPersistenceManager::class))->toBeInstanceOf(PromptPersistenceManager::class)
        ->and($app->make(PromptContentStore::class))->toBeInstanceOf(NullPromptContentStore::class);
});

it('binds prompt content store to generic eloquent store when persistence model is configured', function (): void {
    config(['prompt-registry.persistence.model' => TestPromptRecord::class]);

    /** @var Application $app */
    $app = app();
    $app->forgetInstance('prompt-registry');

    /** @var PromptRegistryServiceProvider $sp */
    $sp = new PromptRegistryServiceProvider($app);
    $sp->registeringPackage();
    $sp->bootingPackage();

    expect($app->make(PromptContentStore::class))->toBeInstanceOf(EloquentPromptContentStore::class);
});

final class TestPromptRecord extends Model
{
    protected $table = 'prompts';
}
