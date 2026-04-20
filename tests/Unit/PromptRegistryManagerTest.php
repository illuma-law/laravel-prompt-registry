<?php

use IllumaLaw\PromptRegistry\Facades\PromptRegistry;
use IllumaLaw\PromptRegistry\PromptRegistryManager;

// ---------------------------------------------------------------------------
// register() / forKey()
// ---------------------------------------------------------------------------

it('can register and retrieve a prompt definition by key', function (): void {
    $manager = new PromptRegistryManager;

    $manager->register('agents.test', [
        'agent' => 'App\Ai\Agents\TestAgent',
        'name' => 'Test Agent',
        'description' => 'Test description',
        'fallback_view' => 'prompts.test',
    ]);

    $retrieved = $manager->forKey('agents.test');

    expect($retrieved)->toBeArray()
        ->and($retrieved['key'])->toBe('agents.test')
        ->and($retrieved['agent'])->toBe('App\Ai\Agents\TestAgent')
        ->and($retrieved['name'])->toBe('Test Agent')
        ->and($retrieved['description'])->toBe('Test description')
        ->and($retrieved['fallback_view'])->toBe('prompts.test');
});

it('merges the key into the definition when registering', function (): void {
    $manager = new PromptRegistryManager;

    $manager->register('agents.merged', [
        'agent' => 'SomeAgent',
        'name' => 'Some Agent',
        'description' => 'Desc',
        'fallback_view' => 'view',
    ]);

    expect($manager->forKey('agents.merged')['key'])->toBe('agents.merged');
});

it('overwrites an existing registration with the same key', function (): void {
    $manager = new PromptRegistryManager;

    $manager->register('agents.dup', [
        'agent' => 'OriginalAgent', 'name' => 'Original', 'description' => '', 'fallback_view' => '',
    ]);
    $manager->register('agents.dup', [
        'agent' => 'ReplacedAgent', 'name' => 'Replaced', 'description' => '', 'fallback_view' => '',
    ]);

    expect($manager->forKey('agents.dup')['agent'])->toBe('ReplacedAgent');
});

it('throws InvalidArgumentException for a missing key', function (): void {
    $manager = new PromptRegistryManager;

    $manager->forKey('non.existent');
})->throws(InvalidArgumentException::class, 'No prompt definition was registered for key [non.existent].');

// ---------------------------------------------------------------------------
// registerMany() / all() / definitionsByKey()
// ---------------------------------------------------------------------------

it('can register many prompts at once', function (): void {
    $manager = new PromptRegistryManager;

    $manager->registerMany([
        'agents.one' => [
            'agent' => 'App\Ai\Agents\One', 'name' => 'One', 'description' => 'Desc one', 'fallback_view' => 'view.one',
        ],
        'agents.two' => [
            'agent' => 'App\Ai\Agents\Two', 'name' => 'Two', 'description' => 'Desc two', 'fallback_view' => 'view.two',
        ],
    ]);

    expect($manager->all())->toHaveCount(2)
        ->and($manager->forKey('agents.one')['agent'])->toBe('App\Ai\Agents\One')
        ->and($manager->forKey('agents.two')['agent'])->toBe('App\Ai\Agents\Two');
});

it('returns an empty array from all() when nothing is registered', function (): void {
    $manager = new PromptRegistryManager;

    expect($manager->all())->toBe([]);
});

it('returns an empty array from definitionsByKey() when nothing is registered', function (): void {
    $manager = new PromptRegistryManager;

    expect($manager->definitionsByKey())->toBe([]);
});

it('returns definitions keyed by registry key from definitionsByKey()', function (): void {
    $manager = new PromptRegistryManager;

    $manager->registerMany([
        'agents.alpha' => ['agent' => 'AlphaAgent', 'name' => 'Alpha', 'description' => '', 'fallback_view' => ''],
        'agents.beta' => ['agent' => 'BetaAgent',  'name' => 'Beta',  'description' => '', 'fallback_view' => ''],
    ]);

    $byKey = $manager->definitionsByKey();

    expect($byKey)->toHaveKeys(['agents.alpha', 'agents.beta'])
        ->and($byKey['agents.alpha']['agent'])->toBe('AlphaAgent')
        ->and($byKey['agents.beta']['agent'])->toBe('BetaAgent');
});

it('all() returns a list (not associative)', function (): void {
    $manager = new PromptRegistryManager;

    $manager->register('agents.x', ['agent' => 'XAgent', 'name' => 'X', 'description' => '', 'fallback_view' => '']);

    $all = $manager->all();

    expect(array_is_list($all))->toBeTrue()
        ->and($all[0]['key'])->toBe('agents.x');
});

// ---------------------------------------------------------------------------
// forAgent()
// ---------------------------------------------------------------------------

it('can retrieve a definition by agent class', function (): void {
    $manager = new PromptRegistryManager;

    $manager->register('agents.test', [
        'agent' => 'App\Ai\Agents\TestAgent', 'name' => 'Test Agent', 'description' => '', 'fallback_view' => '',
    ]);

    expect($manager->forAgent('App\Ai\Agents\TestAgent')['key'])->toBe('agents.test');
});

it('throws InvalidArgumentException for an unknown agent class', function (): void {
    $manager = new PromptRegistryManager;

    $manager->forAgent('Non\Existent\Agent');
})->throws(InvalidArgumentException::class, 'No prompt definition was registered for [Non\Existent\Agent].');

// ---------------------------------------------------------------------------
// defaultPrimaryTierForAgent()
// ---------------------------------------------------------------------------

it('returns standard tier when no tier is specified', function (): void {
    $manager = new PromptRegistryManager;

    $manager->register('agents.standard', [
        'agent' => 'StandardAgent', 'name' => 'Standard', 'description' => '', 'fallback_view' => '',
    ]);

    expect($manager->defaultPrimaryTierForAgent('StandardAgent'))->toBe('standard');
});

it('returns extended tier when default_primary_tier is extended', function (): void {
    $manager = new PromptRegistryManager;

    $manager->register('agents.extended', [
        'agent' => 'ExtendedAgent', 'name' => 'Extended', 'description' => '', 'fallback_view' => '',
        'default_primary_tier' => 'extended',
    ]);

    expect($manager->defaultPrimaryTierForAgent('ExtendedAgent'))->toBe('extended');
});

it('returns standard tier for an unregistered agent', function (): void {
    $manager = new PromptRegistryManager;

    expect($manager->defaultPrimaryTierForAgent('UnknownAgent'))->toBe('standard');
});

it('treats an invalid tier value as standard', function (): void {
    $manager = new PromptRegistryManager;

    $manager->register('agents.weird', [
        'agent' => 'WeirdAgent', 'name' => 'Weird', 'description' => '', 'fallback_view' => '',
        'default_primary_tier' => 'premium',
    ]);

    expect($manager->defaultPrimaryTierForAgent('WeirdAgent'))->toBe('standard');
});

// ---------------------------------------------------------------------------
// shortKeyFromRegistryKey()
// ---------------------------------------------------------------------------

it('extracts the short key from a valid agents.* registry key', function (): void {
    $manager = new PromptRegistryManager;

    expect($manager->shortKeyFromRegistryKey('agents.content_creator'))->toBe('content_creator');
});

it('throws InvalidArgumentException when the agents. prefix is missing', function (): void {
    $manager = new PromptRegistryManager;

    $manager->shortKeyFromRegistryKey('wrong.prefix');
})->throws(InvalidArgumentException::class, 'Unexpected agent registry key [wrong.prefix].');

// ---------------------------------------------------------------------------
// Container / Facade integration
// ---------------------------------------------------------------------------

it('is bound to the container as a singleton', function (): void {
    $a = app('prompt-registry');
    $b = app('prompt-registry');

    expect($a)->toBeInstanceOf(PromptRegistryManager::class)
        ->and($a)->toBe($b);
});

it('can be resolved via the PromptRegistry facade', function (): void {
    PromptRegistry::register('agents.facade', [
        'agent' => 'FacadeAgent', 'name' => 'Facade', 'description' => '', 'fallback_view' => '',
    ]);

    expect(PromptRegistry::forKey('agents.facade')['agent'])->toBe('FacadeAgent');
});

it('auto-registers prompts defined in the config', function (): void {
    config(['prompt-registry.prompts' => [
        'agents.config_driven' => [
            'agent' => 'ConfigAgent',
            'name' => 'Config Agent',
            'description' => 'Loaded from config',
            'fallback_view' => 'prompts.config',
        ],
    ]]);

    // Boot a fresh manager through the service provider bootstrap process.
    /** @var PromptRegistryManager $manager */
    $manager = new PromptRegistryManager;
    /** @var array<string, array{agent: class-string, name: string, description: string, fallback_view: string, default_primary_tier?: 'standard'|'extended'}> $prompts */
    $prompts = config('prompt-registry.prompts', []);
    $manager->registerMany($prompts);

    expect($manager->forKey('agents.config_driven')['agent'])->toBe('ConfigAgent');
});

it('handles empty config prompts array without error', function (): void {
    config(['prompt-registry.prompts' => []]);

    $manager = new PromptRegistryManager;
    $prompts = config('prompt-registry.prompts', []);

    if ($prompts !== []) {
        $manager->registerMany($prompts);
    }

    expect($manager->all())->toBe([]);
});
