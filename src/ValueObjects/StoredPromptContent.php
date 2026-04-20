<?php

declare(strict_types=1);

namespace IllumaLaw\PromptRegistry\ValueObjects;

final class StoredPromptContent
{
    public function __construct(
        public readonly string $key,
        public readonly string $content,
        public readonly bool $exists = true,
    ) {}

    public static function missing(string $key): self
    {
        return new self(
            key: $key,
            content: '',
            exists: false,
        );
    }

    public function hasBody(): bool
    {
        return trim($this->content) !== '';
    }
}
