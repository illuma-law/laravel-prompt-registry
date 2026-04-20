<?php

declare(strict_types=1);

namespace IllumaLaw\PromptRegistry;

use Illuminate\Support\Str;

final class AgentKeyResolver
{
    public function __construct(
        private readonly PromptRegistryManager $registry,
    ) {}

    /**
     * @param  class-string  $agentClass
     */
    public function shortKeyForAgent(string $agentClass): string
    {
        try {
            $definition = $this->registry->forAgent($agentClass);

            return $this->registry->shortKeyFromRegistryKey($definition['key']);
        } catch (\Throwable) {
            return Str::snake(str_replace('Agent', '', class_basename($agentClass)));
        }
    }

    /**
     * @param  class-string  $agentClass
     * @return 'standard'|'extended'
     */
    public function defaultPrimaryTierForAgent(string $agentClass): string
    {
        return $this->registry->defaultPrimaryTierForAgent($agentClass);
    }
}
