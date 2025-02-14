<?php

$content = \file_get_contents(dirname(__DIR__) . '/composer.lock');
$dependencies = \json_decode($content, true, 512, \JSON_THROW_ON_ERROR);

$expectedDependencies = [
    'doctrine/annotations' => '2.0.0',
    'doctrine/lexer' => '3.0.0',
    'doctrine/dbal' => '3.6.0',
    'doctrine/orm' => '3.0.0',
    'doctrine/data-fixtures' => '1.8.0'
];

$unmetDependencies = [];
foreach ($expectedDependencies as $expectedDependency => $expectedVersion) {
    $dependency = null;
    foreach ([...$dependencies['packages'], ...$dependencies['packages-dev']] as $package) {
        if ($package['name'] === $expectedDependency) {
            $dependency = $package;
            break;
        }
    }

    if (null === $dependency) {
        throw new \RuntimeException('Missing dependency: ' . $expectedDependency);
    }

    $dependencyVersion = $dependency['version'];

    if ($dependencyVersion !== $expectedVersion) {
        $unmetDependencies[] = sprintf(
            "Invalid version for %s. Expected: %s. Found: %s",
            $expectedDependency,
            $expectedVersion,
            $dependencyVersion
        );
    }
}

if ($unmetDependencies) {
    throw new \RuntimeException(implode(\PHP_EOL, $unmetDependencies));
}

echo 'All dependencies are correct.' . \PHP_EOL;
