<?php

declare(strict_types=1);

namespace IllumaLaw\PromptRegistry\Facades;

use IllumaLaw\PromptRegistry\PromptRegistryManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(string $key, array{agent: class-string, name: string, description: string, fallback_view: string, default_primary_tier?: 'standard'|'extended'} $definition)
 * @method static void registerMany(array<string, array{agent: class-string, name: string, description: string, fallback_view: string, default_primary_tier?: 'standard'|'extended'}> $definitions)
 * @method static array<string, array{agent: class-string, key: string, name: string, description: string, fallback_view: string, default_primary_tier?: 'standard'|'extended'}> definitionsByKey()
 * @method static array<int, array{agent: class-string, key: string, name: string, description: string, fallback_view: string, default_primary_tier?: 'standard'|'extended'}> all()
 * @method static array{agent: class-string, key: string, name: string, description: string, fallback_view: string, default_primary_tier?: 'standard'|'extended'} forAgent(string $agentClass)
 * @method static array{agent: class-string, key: string, name: string, description: string, fallback_view: string, default_primary_tier?: 'standard'|'extended'} forKey(string $key)
 * @method static string defaultPrimaryTierForAgent(string $agentClass)
 * @method static string shortKeyFromRegistryKey(string $registryKey)
 *
 * @see PromptRegistryManager
 */
class PromptRegistry extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'prompt-registry';
    }
}
