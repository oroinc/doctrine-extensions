<?php
declare(strict_types=1);

namespace Oro\Tests\Connection\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Oro\Entities\Foo;

class LoadFooData implements FixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $fixtures = [];
        $fixtures[] = $this->getFoo('test', 100.0, new \DateTime('2014-01-04 05:06:07'), 'code');
        $fixtures[] = $this->getFoo('test', -10.0, new \DateTime('2015-04-05 06:07:08'), 'code');
        $fixtures[] = $this->getFoo('test', null, new \DateTime('2015-04-05 06:07:08'), 'code');

        foreach ($fixtures as $fixture) {
            $manager->persist($fixture);
        }
        $manager->flush();
    }

    protected function getFoo($name, $budget, $createdAt, $code): Foo
    {
        $foo = new Foo();
        $foo->setName($name);
        $foo->setBudget((float) $budget);
        $foo->setCreatedAt($createdAt);
        $foo->setCode($code);

        return $foo;
    }
}
