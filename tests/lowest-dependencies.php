<?php

$content = file_get_contents(dirname(__DIR__) . '/composer.lock');

$dependencies = json_decode($content, true, \JSON_THROW_ON_ERROR);

$expectedDependencies = [
    'doctrine/annotations' => '1.14.0',
    'doctrine/lexer' => '2.0.0',
    'doctrine/dbal' => '3.3.6',
    'doctrine/orm' => '2.19.0',
];

foreach ($expectedDependencies as $expectedDependency => $expectedVersion) {
    $dependency = null;
    foreach ([...$dependencies['packages'], ...$dependencies['packages-dev']] as $package) {
        if ($package['name'] === $expectedDependency) {
            $dependency = $package;
            break;
        }
    }

    if (null === $dependency) {
        throw new RuntimeException('Missing dependency: ' . $expectedDependency);
    }

    $dependencyVersion = $dependency['version'];

    if ($dependencyVersion !== $expectedVersion) {
        throw new RuntimeException(
            sprintf(
                "Invalid version for %s. Expected: %s. Found: %s",
                $expectedDependency,
                $expectedVersion,
                $dependencyVersion
            )
        );
    }
}

echo 'All dependencies are correct.' . \PHP_EOL;
