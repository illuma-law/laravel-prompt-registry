<?php

declare(strict_types=1);

use IllumaLaw\PromptRegistry\NullPromptContentStore;
use IllumaLaw\PromptRegistry\ValueObjects\StoredPromptContent;

it('marks missing stored prompt content as non-existent with an empty body', function (): void {
    $missing = StoredPromptContent::missing('agents.example');

    expect($missing->key)->toBe('agents.example')
        ->and($missing->exists)->toBeFalse()
        ->and($missing->content)->toBe('')
        ->and($missing->hasBody())->toBeFalse();
});

it('treats whitespace-only content as empty body', function (): void {
    $stored = new StoredPromptContent(
        key: 'agents.example',
        content: "   \n\t",
        exists: true,
    );

    expect($stored->hasBody())->toBeFalse();
});

it('null prompt content store returns null for missing keys and echoes upserts', function (): void {
    $store = new NullPromptContentStore;

    expect($store->findByKey('agents.unknown'))->toBeNull();

    $stored = $store->upsertByKey('agents.unknown', 'Prompt body');

    expect($stored->key)->toBe('agents.unknown')
        ->and($stored->content)->toBe('Prompt body')
        ->and($stored->exists)->toBeTrue()
        ->and($stored->hasBody())->toBeTrue();
});
