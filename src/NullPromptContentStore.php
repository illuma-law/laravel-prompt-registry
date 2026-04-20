<?php

declare(strict_types=1);

namespace IllumaLaw\PromptRegistry;

use IllumaLaw\PromptRegistry\Contracts\PromptContentStore;
use IllumaLaw\PromptRegistry\ValueObjects\StoredPromptContent;

final class NullPromptContentStore implements PromptContentStore
{
    public function findByKey(string $key): ?StoredPromptContent
    {
        return null;
    }

    public function upsertByKey(string $key, string $content): StoredPromptContent
    {
        return new StoredPromptContent(
            key: $key,
            content: $content,
            exists: true,
        );
    }

    public function deleteByKey(string $key): void
    {
        //
    }
}
