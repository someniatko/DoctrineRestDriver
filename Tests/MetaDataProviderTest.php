<?php
/**
 * This file is part of DoctrineRestDriver.
 *
 * DoctrineRestDriver is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * DoctrineRestDriver is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with DoctrineRestDriver.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Circle\DoctrineRestDriver\Tests;

use Circle\DoctrineRestDriver\MetaDataProvider;

/**
 * Tests the meta data provider
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 *
 * @coversDefaultClass Circle\DoctrineRestDriver\MetaDataProvider
 */
class MetaDataProviderTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var MetaDataProvider
     */
    private $provider;

    /**
     * {@inheritdoc}
     */
    public function setUp() {
        $this->provider = new MetaDataProvider();
    }

    /**
     * @test
     * @group  unit
     * @covers ::__construct
     * @covers ::getEntityNamespaces
     * @covers \Circle\DoctrineRestDriver\Annotations\Loader::load
     */
    public function getEntityNamespaces() {
        $entities = [
            'categories'     => 'Circle\DoctrineRestDriver\Tests\Entity\AssociatedEntity',
            'nonImplemented' => 'Circle\DoctrineRestDriver\Tests\Entity\NonImplementedEntity',
            'products'       => 'Circle\DoctrineRestDriver\Tests\Entity\TestEntity',
        ];
        $this->assertEquals($entities, $this->provider->getEntityNamespaces());
    }
}