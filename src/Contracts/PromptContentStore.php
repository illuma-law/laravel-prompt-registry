<?php

declare(strict_types=1);

namespace IllumaLaw\PromptRegistry\Contracts;

use IllumaLaw\PromptRegistry\ValueObjects\StoredPromptContent;

interface PromptContentStore
{
    public function findByKey(string $key): ?StoredPromptContent;

    public function upsertByKey(string $key, string $content): StoredPromptContent;

    public function deleteByKey(string $key): void;
}
