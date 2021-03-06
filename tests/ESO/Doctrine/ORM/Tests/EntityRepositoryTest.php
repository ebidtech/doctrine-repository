<?php

/*
 * This file is a part of the Doctrine Repository library.
 *
 * (c) 2013 Eduardo Oliveira <entering@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ESO\Doctrine\ORM\Tests;

use ESO\IReflection\ReflClass;
use Doctrine\ORM\Query\Expr\From;
use Doctrine\ORM\Query\Expr\Select;
use ESO\Doctrine\ORM\Tests\Models\ModelEntityRepositoryInterface;

/**
 * EntityRepository
 */
class EntityRepositoryTest extends TestCase
{
    /**
     * @param string $model
     *
     * @dataProvider providerModelClassNames
     */
    public function testEntityNameAlias($model)
    {
        $entityManager = ReflClass::create('Doctrine\ORM\EntityManager')->newInstanceWithoutConstructor();
        $entityName = 'ModelA';
        $alias = 'a';

        /** @var ModelEntityRepositoryInterface $repository */
        $repository = new $model($entityManager, 'ModelA', 'a');

        $qb = $repository->getByIdQB(1);

        $this->assertInstanceOf('ESO\Doctrine\ORM\QueryBuilder', $qb);

        $dqlParts = $qb->getDQLParts();
        $this->assertArrayHasKey('select', $dqlParts);
        $this->assertArrayHasKey('from', $dqlParts);

        $this->assertCount(1, $dqlParts['select']);
        $this->assertArrayHasKey(0, $dqlParts['select']);
        /** @var Select $select */
        $select = $dqlParts['select'][0];
        $selectParts = $select->getParts();
        $this->assertCount(1, $selectParts);
        $this->assertArrayHasKey(0, $selectParts);
        $this->assertEquals($alias, $selectParts[0]);

        $this->assertCount(1, $dqlParts['from']);
        $this->assertArrayHasKey(0, $dqlParts['from']);
        /** @var From $from */
        $from = $dqlParts['from'][0];
        $this->assertInstanceOf('Doctrine\ORM\Query\Expr\From', $from);

        $this->assertEquals($entityName, $from->getFrom());
        $this->assertEquals($alias, $from->getAlias());
    }

    public function providerModelClassNames()
    {
        return array(
            array('ESO\Doctrine\ORM\Tests\Models\ModelAEntityRepository'),
            array('ESO\Doctrine\ORM\Tests\Models\ModelBEntityRepository')
        );
    }
}
