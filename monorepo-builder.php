<?php

declare(strict_types=1);

use Symplify\MonorepoBuilder\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\MonorepoBuilder\Config\MBConfig;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\AddTagToChangelogReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\PushNextDevReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\PushTagReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetCurrentMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetNextMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\TagVersionReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateBranchAliasReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateReplaceReleaseWorker;

return static function (MBConfig $mbConfig): void {

    $mbConfig->workers([
        UpdateReplaceReleaseWorker::class,
        SetCurrentMutualDependenciesReleaseWorker::class,
        AddTagToChangelogReleaseWorker::class,
        TagVersionReleaseWorker::class,
        PushTagReleaseWorker::class,
        SetNextMutualDependenciesReleaseWorker::class,
        UpdateBranchAliasReleaseWorker::class,
        PushNextDevReleaseWorker::class,
    ]);

    $mbConfig->packageDirectories([
        __DIR__.'/packages',
        __DIR__.'/modules',
    ]);

    $mbConfig->dataToAppend([
        ComposerJsonSection::REQUIRE_DEV => [
            'barryvdh/laravel-ide-helper' => '^3.0',
            'larastan/larastan' => '^2.0',
            'laravel-json-api/testing' => '^3.0',
            'laravel/pint' => '^1.7',
            'orchestra/testbench' => '^9.0',
            'pestphp/pest' => '^2.0',
            'pestphp/pest-plugin-faker' => '^2.0',
            'pestphp/pest-plugin-laravel' => '^2.0',
            'spatie/laravel-ray' => '^1.32',
            'symplify/monorepo-builder' => '^11.2',
        ],
    ]);

};
