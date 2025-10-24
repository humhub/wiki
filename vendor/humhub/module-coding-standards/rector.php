<?php

declare(strict_types=1);

require_once __DIR__ . '/../../autoload.php';

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;

/**
 * Minimum PHP Version required for current Rector rules
 *
 * @note Also adjust the PhpSets in the RectorConfig::configure() method accordingly
 */
$minPhpVersion = '8.2';

/**
 * Minimum HumHub Version required for current Rector rules
 */
$minHumHubVersion = '1.18';


\HumHubUtils\UpdatePhpVersion::increaseVersion($minPhpVersion);
\HumHubUtils\UpdateHumHubMinVersion::increaseVersion($minHumHubVersion);


return RectorConfig::configure()
    ->withPaths([
        getcwd(),
    ])
    ->withSkip([
        \Rector\Php81\Rector\Array_\FirstClassCallableRector::class,
        \Rector\TypeDeclaration\Rector\ClassMethod\ReturnNeverTypeRector::class,
        \Rector\DeadCode\Rector\StaticCall\RemoveParentCallWithoutParentRector::class,
        getcwd() . '/vendor',
        getcwd() . '/messages',
    ])
    ->withPhpSets(php82: true)
    ->withTypeCoverageLevel(0)
    ->withDeadCodeLevel(0)
    ->withCodeQualityLevel(0)
    ->withRules([
        // Some own rules
    ])
    ->withConfiguredRule(
        RenameClassRector::class,
        [
            //'OldNamespace\\OldClass' => 'NewNamespace\\NewClass',
        ]
    );