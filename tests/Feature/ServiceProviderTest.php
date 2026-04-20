<?php

use IllumaLaw\PromptRegistry\PromptRegistryManager;
use IllumaLaw\PromptRegistry\PromptRegistryServiceProvider;
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
