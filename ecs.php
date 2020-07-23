<?php

declare(strict_types=1);

use SlevomatCodingStandard\Sniffs\Classes\UnusedPrivateElementsSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\CodingStandard\Sniffs\Debug\CommentedOutCodeSniff;
use Symplify\EasyCodingStandard\Configuration\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::SETS, [
        SetList::PHP_70,
        SetList::PHP_71,
        SetList::CLEAN_CODE,
        SetList::SYMPLIFY,
        SetList::COMMON,
        SetList::PSR_12,
    ]);

    $parameters->set(Option::PATHS, [__DIR__ . '/packages']);

    $parameters->set(Option::SKIP, [
        UnusedPrivateElementsSniff::class . '.' . UnusedPrivateElementsSniff::CODE_WRITE_ONLY_PROPERTY => [
            'packages/symfony-route-usage/src/Entity/RouteVisit.php'
        ],
        UnusedPrivateElementsSniff::class . '.' . UnusedPrivateElementsSniff::CODE_UNUSED_PROPERTY => [
            'packages/symfony-route-usage/src/Entity/RouteVisit.php'
        ],
        CommentedOutCodeSniff::class => [
            __DIR__ . '/packages/latte-to-twig/src/CaseConverter/*',
        ]
    ]);

    $services = $containerConfigurator->services();

    $services->set(LineLengthFixer::class);
};
