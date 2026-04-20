<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Agent Prompts
    |--------------------------------------------------------------------------
    |
    | Here you may define the prompts for your AI agents. Each prompt should
    | have a unique key and specify the agent class, name, description,
    | and a fallback blade view for the prompt content.
    |
    */

    'prompts' => [
        // 'agents.example' => [
        //     'agent'         => \App\Ai\Agents\ExampleAgent::class,
        //     'name'          => 'Example Agent',
        //     'description'   => 'Example description.',
        //     'fallback_view' => 'prompts.agents.example',
        //     'default_primary_tier' => 'standard',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Prompt Persistence
    |--------------------------------------------------------------------------
    |
    | Configure an Eloquent model used to persist prompt overrides. When
    | provided, the package will bind PromptContentStore to the generic
    | EloquentPromptContentStore automatically.
    |
    */
    'persistence' => [
        'model' => null,
    ],
];
