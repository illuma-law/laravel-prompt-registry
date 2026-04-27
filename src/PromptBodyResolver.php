<?php

declare(strict_types=1);

namespace IllumaLaw\PromptRegistry;

use IllumaLaw\PromptRegistry\Contracts\PromptContentStore;
use IllumaLaw\PromptRegistry\ValueObjects\StoredPromptContent;

final class PromptBodyResolver
{
    public function __construct(
        private readonly PromptRegistryManager $registry,
        private readonly PromptContentStore $contentStore,
        private readonly PromptTemplateRenderer $renderer,
    ) {}

    public function resolve(string $key, string $fallbackView): string
    {
        $stored = $this->contentStore->findByKey($key) ?? StoredPromptContent::missing($key);

        if ($stored->hasBody()) {
            return $stored->content;
        }

        return $this->renderer->renderFallback($fallbackView);
    }

    /**
     * @param  class-string  $agentClass
     */
    public function resolveForAgent(string $agentClass): string
    {
        $definition = $this->registry->forAgent($agentClass);

        return $this->resolve($definition['key'], $definition['fallback_view']);
    }

    /**
     * @return array{
     *     key: string,
     *     name: string,
     *     description: string,
     *     fallback_view: string,
     *     content: string,
     *     source: 'database'|'fallback_view',
     *     persisted: bool,
     *     empty_persisted_override: bool
     * }
     */
    public function editableByKey(string $key): array
    {
        $definition = $this->registry->forKey($key);
        $stored = $this->contentStore->findByKey($key) ?? StoredPromptContent::missing($key);
        $fallbackContent = $this->renderer->renderFallback($definition['fallback_view']);

        $hasDatabaseContent = $stored->hasBody();

        return [
            'key' => $definition['key'],
            'name' => $definition['name'],
            'description' => $definition['description'],
            'fallback_view' => $definition['fallback_view'],
            'content' => $hasDatabaseContent ? $stored->content : $fallbackContent,
            'source' => $hasDatabaseContent ? 'database' : 'fallback_view',
            'persisted' => $stored->exists,
            'empty_persisted_override' => $stored->exists && ! $hasDatabaseContent,
        ];
    }
}
