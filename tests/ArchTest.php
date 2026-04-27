<?php

declare(strict_types=1);

arch('source files use strict types')
    ->expect('IllumaLaw\PromptRegistry')
    ->toUseStrictTypes();

arch('no source class extends Illuminate facades directly')
    ->expect('IllumaLaw\PromptRegistry')
    ->not->toExtend('Illuminate\Support\Facades\Facade')
    ->ignoring('IllumaLaw\PromptRegistry\Facades');

arch('facades extend Illuminate Facade')
    ->expect('IllumaLaw\PromptRegistry\Facades')
    ->toExtend('Illuminate\Support\Facades\Facade');

arch('service provider extends spatie PackageServiceProvider')
    ->expect('IllumaLaw\PromptRegistry\PromptRegistryServiceProvider')
    ->toExtend('Spatie\LaravelPackageTools\PackageServiceProvider');

arch('no debug calls are left in source')
    ->expect('IllumaLaw\PromptRegistry')
    ->not->toUse(['dd', 'dump', 'var_dump', 'ray', 'print_r']);
