<?php

use IllumaLaw\PromptRegistry\AgentKeyResolver;
use IllumaLaw\PromptRegistry\PromptRegistryManager;

it('resolves short keys from the registry when an agent is registered', function (): void {
    $manager = new PromptRegistryManager;

    /** @var class-string $agent */
    $agent = 'App\\Ai\\Agents\\ContentCreatorAgent';

    $manager->register('agents.content_creator', [
        'agent' => $agent,
        'name' => 'Content Creator',
        'description' => 'Creates content',
        'fallback_view' => 'prompts.agents.content_creator',
    ]);

    $resolver = new AgentKeyResolver($manager);

    expect($resolver->shortKeyForAgent($agent))->toBe('content_creator');
});

it('falls back to snake_case agent basename when not registered', function (): void {
    $manager = new PromptRegistryManager;
    $resolver = new AgentKeyResolver($manager);

    /** @var class-string $agent */
    $agent = 'App\\Ai\\Agents\\LegalDraftMemoAgent';

    expect($resolver->shortKeyForAgent($agent))->toBe('legal_draft_memo');
});

it('delegates default tier resolution to the registry manager', function (): void {
    $manager = new PromptRegistryManager;

    /** @var class-string $agent */
    $agent = 'App\\Ai\\Agents\\CaseReportCompilerAgent';

    $manager->register('agents.case_report_compiler', [
        'agent' => $agent,
        'name' => 'Case Report Compiler',
        'description' => 'Compiles case reports',
        'fallback_view' => 'prompts.agents.case_report_compiler',
        'default_primary_tier' => 'extended',
    ]);

    $resolver = new AgentKeyResolver($manager);

    expect($resolver->defaultPrimaryTierForAgent($agent))->toBe('extended');
});
