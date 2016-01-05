<?php

namespace Oro\Tests\ORM\AST\Query\Functions;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\Query;

use Symfony\Component\Yaml\Yaml;

use Oro\Tests\Connection\TestUtil;
use Oro\Tests\TestCase;

class FunctionsTest extends TestCase
{
    /**
     * @dataProvider functionsDataProvider
     * @param array $functions
     * @param string $dql
     * @param string $sql
     * @param string $expectedResult
     */
    public function testDqlFunction(array $functions, $dql, $sql, $expectedResult)
    {
        $configuration = $this->entityManager->getConfiguration();

        foreach ($functions as $function) {
            $this->registerDqlFunction($function['type'], $function['name'], $function['className'], $configuration);
        }

        $query = new Query($this->entityManager);
        $query->setDQL($dql);

        if (is_array($sql)) {
            $constraints = array();
            foreach ($sql as $sqlVariant) {
                $constraints[] = $this->equalTo($sqlVariant);
            }
            $constraint = new \PHPUnit_Framework_Constraint_Or();
            $constraint->setConstraints($constraints);
            $this->assertThat($query->getSQL(), $constraint);
        } else {
            $this->assertEquals($sql, $query->getSQL(), sprintf('Unexpected SQL for "%s"', $dql));
        }
        $result = $query->getArrayResult();
        $this->assertNotEmpty($result);
        $this->assertEquals(
            $expectedResult,
            array_values(array_shift($result)),
            sprintf('Unexpected result for "%s"', $dql)
        );
    }

    /**
     * @return array
     */
    public function functionsDataProvider()
    {
        $platform = TestUtil::getPlatformName();
        $data = array();
        $files = new \FilesystemIterator(__DIR__ . '/fixtures/' . $platform, \FilesystemIterator::SKIP_DOTS);
        foreach ($files as $file) {
            $fileData = Yaml::parse($file);
            if (!is_array($fileData)) {
                throw new \RuntimeException(sprintf('Could not parse file %s', $file));
            }
            $data = array_merge($data, $fileData);
        }

        return $data;
    }

    /**
     * @param string $type
     * @param string $functionName
     * @param string $functionClass
     * @param Configuration $configuration
     */
    protected function registerDqlFunction($type, $functionName, $functionClass, Configuration $configuration)
    {
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
