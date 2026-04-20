# Laravel Prompt Registry

[![Run Tests](https://github.com/illuma-law/laravel-prompt-registry/actions/workflows/run-tests.yml/badge.svg)](https://github.com/illuma-law/laravel-prompt-registry/actions/workflows/run-tests.yml)
[![PHPStan](https://github.com/illuma-law/laravel-prompt-registry/actions/workflows/phpstan.yml/badge.svg)](https://github.com/illuma-law/laravel-prompt-registry/actions/workflows/phpstan.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/illuma-law/laravel-prompt-registry.svg?style=flat-square)](https://packagist.org/packages/illuma-law/laravel-prompt-registry)
[![Total Downloads](https://img.shields.io/packagist/dt/illuma-law/laravel-prompt-registry.svg?style=flat-square)](https://packagist.org/packages/illuma-law/laravel-prompt-registry)
[![License](https://img.shields.io/packagist/l/illuma-law/laravel-prompt-registry.svg?style=flat-square)](https://packagist.org/packages/illuma-law/laravel-prompt-registry)

A lightweight, dynamic registry for AI agent prompt definitions in Laravel. Register prompt metadata — agent class, name, description, fallback Blade view, and tier — either via configuration or programmatically in a service provider, then look them up at runtime from anywhere in your application.

## TL;DR

```php
use IllumaLaw\PromptRegistry\Facades\PromptRegistry;

// Register a prompt definition
PromptRegistry::register('agents.legal_advisor', [
    'agent' => \App\Ai\Agents\LegalAdvisor::class,
    'name' => 'Legal Advisor',
    'description' => 'Provides initial legal guidance.',
    'fallback_view' => 'prompts.legal_advisor',
]);

// Retrieve it later
$definition = PromptRegistry::forKey('agents.legal_advisor');
```

## Requirements

| Dependency | Version |
|---|---|
| PHP | ^8.3 |
| Laravel | 11, 12, or 13 |

## Installation

Install the package via Composer:

```bash
composer require illuma-law/laravel-prompt-registry
```

The service provider and `PromptRegistry` facade alias are registered automatically via Laravel's package auto-discovery.

### Publish the Config File

```bash
php artisan vendor:publish --tag="laravel-prompt-registry-config"
```

This creates `config/prompt-registry.php` in your application.

## Configuration

`config/prompt-registry.php` holds a `prompts` array where each key is a dot-notation registry key and each value describes the prompt definition:

```php
return [
    'prompts' => [
        'agents.content_creator' => [
            'agent'               => \App\Ai\Agents\ContentCreatorAgent::class,
            'name'                => 'Content Creator Agent',
            'description'        => 'Generates marketing social content.',
            'fallback_view'      => 'prompts.agents.content_creator',
            'default_primary_tier' => 'standard', // 'standard' | 'extended' (optional)
        ],
    ],
];
```

All prompts defined here are automatically registered during the package boot phase.

## Usage

### Registering Prompts Programmatically

Register a single prompt anywhere (e.g. `AppServiceProvider::boot()`):

```php
use IllumaLaw\PromptRegistry\Facades\PromptRegistry;

PromptRegistry::register('agents.custom', [
    'agent'         => \App\Ai\Agents\CustomAgent::class,
    'name'          => 'Custom Agent',
    'description'   => 'A custom agent for specialized tasks.',
    'fallback_view' => 'prompts.agents.custom',
]);
```

Register multiple prompts at once:

```php
PromptRegistry::registerMany([
    'agents.summariser' => [
        'agent'         => \App\Ai\Agents\SummariserAgent::class,
        'name'          => 'Summariser',
        'description'   => 'Summarises legal documents.',
        'fallback_view' => 'prompts.agents.summariser',
    ],
    'agents.classifier' => [
        'agent'         => \App\Ai\Agents\ClassifierAgent::class,
        'name'          => 'Classifier',
        'description'   => 'Classifies legal texts by category.',
        'fallback_view' => 'prompts.agents.classifier',
        'default_primary_tier' => 'extended',
    ],
]);
```

### Retrieving Prompts

```php
use IllumaLaw\PromptRegistry\Facades\PromptRegistry;

// All registered prompts as a flat list
$all = PromptRegistry::all();

// All registered prompts keyed by their registry key
$byKey = PromptRegistry::definitionsByKey();

// Look up a specific prompt by its registry key
$definition = PromptRegistry::forKey('agents.content_creator');
// $definition['key']          => 'agents.content_creator'
// $definition['agent']        => \App\Ai\Agents\ContentCreatorAgent::class
// $definition['name']         => 'Content Creator Agent'
// $definition['description']  => 'Generates marketing social content.'
// $definition['fallback_view']=> 'prompts.agents.content_creator'

// Look up a prompt by agent class
$definition = PromptRegistry::forAgent(\App\Ai\Agents\ContentCreatorAgent::class);

// Get the default primary tier for an agent ('standard' or 'extended')
$tier = PromptRegistry::defaultPrimaryTierForAgent(\App\Ai\Agents\ContentCreatorAgent::class);

// Extract the short key from a registry key (strips the 'agents.' prefix)
$shortKey = PromptRegistry::shortKeyFromRegistryKey('agents.content_creator');
// => 'content_creator'
```

### Exception Handling

`forKey()` and `forAgent()` both throw `\InvalidArgumentException` when no matching definition is found. `shortKeyFromRegistryKey()` throws `\InvalidArgumentException` when the key does not start with `agents.`.

```php
use IllumaLaw\PromptRegistry\Facades\PromptRegistry;

try {
    $definition = PromptRegistry::forKey('agents.unknown');
} catch (\InvalidArgumentException $e) {
    // No prompt definition was registered for key [agents.unknown].
}
```

### Direct Manager Access

You can resolve the underlying `PromptRegistryManager` from the container directly if you prefer dependency injection:

```php
use IllumaLaw\PromptRegistry\PromptRegistryManager;

class MyService
{
    public function __construct(protected PromptRegistryManager $registry) {}

    public function handle(): void
    {
        $definition = $this->registry->forKey('agents.content_creator');
    }
}
```

Or via the string binding:

```php
$manager = app('prompt-registry');
```

## Testing

```bash
# Run the full test suite with coverage
composer test

# Run static analysis
composer analyse

# Fix code style
composer format
```

The test suite uses [Pest](https://pestphp.com/) and requires 100% code coverage (`--min=100`).

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

If you discover a security vulnerability, please send an e-mail to [support@illuma.law](mailto:support@illuma.law). All security vulnerabilities will be promptly addressed.

## Credits

- [illuma-law](https://github.com/illuma-law)
- [All Contributors](https://github.com/illuma-law/laravel-prompt-registry/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
