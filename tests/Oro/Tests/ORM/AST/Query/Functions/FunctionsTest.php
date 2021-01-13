<?php
declare(strict_types=1);

namespace Oro\Tests\ORM\AST\Query\Functions;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\Query;

use PHPUnit\Framework\Constraint\LogicalOr;
use Symfony\Component\Yaml\Yaml;

use Oro\Tests\Connection\TestUtil;
use Oro\Tests\TestCase;

class FunctionsTest extends TestCase
{
    /**
     * @dataProvider functionsDataProvider
     * @param string|string[] $sql
     */
    public function testDqlFunction(array $functions, string $dql, $sql, array $expectedResult): void
    {
        $configuration = $this->entityManager->getConfiguration();

        foreach ($functions as $function) {
            $this->registerDqlFunction($function['type'], $function['name'], $function['className'], $configuration);
        }

        $query = new Query($this->entityManager);
        $query->setDQL($dql);

        if (\is_array($sql)) {
            $constraints = [];
            foreach ($sql as $sqlVariant) {
                $constraints[] = static::equalTo($sqlVariant);
            }
            $constraint = new LogicalOr();
            $constraint->setConstraints($constraints);
            static::assertThat($query->getSQL(), $constraint);
        } else {
            static::assertEquals($sql, $query->getSQL(), \sprintf('Unexpected SQL for "%s"', $dql));
        }
        $result = $query->getArrayResult();
        static::assertNotEmpty($result);
        static::assertEquals(
            $expectedResult,
            \array_values(\array_shift($result)),
            \sprintf('Unexpected result for "%s"', $dql)
        );
    }

    /**
     * @throws \Exception
     */
    public function functionsDataProvider(): array
    {
        $platform = TestUtil::getPlatformName();
        $data = [];
        $files = new \FilesystemIterator(
            __DIR__ . '/fixtures/' . $platform,
            \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::CURRENT_AS_PATHNAME
        );
        foreach ($files as $file) {
            $fileData = Yaml::parseFile($file);
            if (!\is_array($fileData)) {
                throw new \RuntimeException(\sprintf('Could not parse file %s', $file));
            }
            /** @noinspection SlowArrayOperationsInLoopInspection */
            $data = \array_merge($data, $fileData);
        }

        return $data;
    }

    protected function registerDqlFunction(
        string $type,
        string $functionName,
        string $functionClass,
        Configuration $configuration
    ): void {
        switch ($type) {
            case 'datetime':
                $configuration->addCustomDatetimeFunction($functionName, $functionClass);
                break;
            case 'numeric':
                $configuration->addCustomNumericFunction($functionName, $functionClass);
                break;
            case 'string':
            default:
                $configuration->addCustomStringFunction($functionName, $functionClass);
        }
    }
}
