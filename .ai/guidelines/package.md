---
description: Centralized AI agent prompt definition registry for Laravel — config-driven, tier routing, facade access
---

# laravel-prompt-registry

Centralized registry for AI agent prompt definitions. Registers agent metadata (class, name, description, Blade fallback view, LLM tier) from config or service providers.

## Namespace

`IllumaLaw\PromptRegistry`

## Key Classes & Facades

- `PromptRegistry` facade — primary entry point
- `PromptRegistryManager` — injectable for testing
- `PromptBodyResolver` — resolves prompt body text for an agent
- `PromptPersistenceManager` — persists/retrieves runtime prompt overrides via Eloquent
- `AgentKeyResolver` — converts agent class ↔ short key, looks up default tier

## Config

Publish: `php artisan vendor:publish --tag="laravel-prompt-registry-config"`

```php
// config/prompt-registry.php
return [
    'prompts' => [
        'agents.my_agent' => [
            'agent'                => \App\Ai\Agents\MyAgent::class,
            'name'                 => 'My Agent',
            'description'          => 'Does X.',
            'fallback_view'        => 'prompts.agents.my_agent',
            'default_primary_tier' => 'standard', // 'standard' | 'extended'
        ],
    ],
];
```

## Facade Usage

```php
use IllumaLaw\PromptRegistry\Facades\PromptRegistry;

// Get all registered definitions
$definitions = PromptRegistry::definitionsByKey();

// Look up by agent class
$def = PromptRegistry::definitionForAgent(MyAgent::class);

// Resolve default tier for an agent
$tier = PromptRegistry::defaultPrimaryTierForAgent(MyAgent::class); // 'standard'|'extended'
```

## Dynamic Registration (ServiceProvider)

```php
use IllumaLaw\PromptRegistry\Facades\PromptRegistry;

PromptRegistry::register('agents.my_dynamic_agent', [
    'agent'                => MyDynamicAgent::class,
    'name'                 => 'Dynamic Agent',
    'fallback_view'        => 'prompts.agents.my_dynamic_agent',
    'default_primary_tier' => 'extended',
]);
```

## Resolving Prompt Body (AgentUserPromptView / PromptBodyResolver)

The app resolves prompt content via `App\Services\PromptResolver` which wraps `PromptBodyResolver`. The resolver checks the DB for runtime overrides (persisted via Hub) before falling back to the Blade view.

## AgentKeyResolver

```php
use IllumaLaw\PromptRegistry\AgentKeyResolver;

$resolver = app(AgentKeyResolver::class);
$short = $resolver->shortKeyForAgent(MyAgent::class);  // 'my_agent'
$tier  = $resolver->defaultPrimaryTierForAgent(MyAgent::class); // 'standard'
```
