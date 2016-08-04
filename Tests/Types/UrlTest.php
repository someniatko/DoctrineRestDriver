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

namespace Circle\DoctrineRestDriver\Tests\Types;

use Circle\DoctrineRestDriver\Types\Url;
use PHPSQLParser\PHPSQLParser;

/**
 * Tests the url type
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 *
 * @coversDefaultClass Circle\DoctrineRestDriver\Types\Url
 */
class UrlTest extends \PHPUnit_Framework_TestCase {

    /**
     * @test
     * @group  unit
     * @covers ::create
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function create() {
        $this->assertSame('http://circle.ai/products/1', Url::create('products', 'http://circle.ai', '1'));
    }

    /**
     * @test
     * @group  unit
     * @covers ::create
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function createWithUrl() {
        $this->assertSame('http://www.circle.ai/products/1', Url::create('http://www.circle.ai/products/{id}', 'http://circle.ai', '1'));
    }

    /**
     * @test
     * @group  unit
     * @covers ::create
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function createWithUrlWithoutSetId() {
        $this->assertSame('http://www.circle.ai/products', Url::create('http://www.circle.ai/products/{id}', 'http://circle.ai'));
    }

    /**
     * @test
     * @group  unit
     * @covers ::create
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function createWithoutAnyId() {
        $this->assertSame('http://www.circle.ai/products/1', Url::create('http://www.circle.ai/products', 'http://circle.ai', '1'));
    }

    /**
     * @test
     * @group  unit
     * @covers ::createFromTokens
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function createFromTokens() {
        $tokens     = (new PHPSQLParser())->parse('SELECT name FROM products WHERE id=1');
        $annotation = $this->getMockBuilder('Circle\DoctrineRestDriver\Annotations\DataSource')->getMock();
        $annotation
            ->expects($this->exactly(2))
            ->method('getRoute')
            ->will($this->returnValue('http://circle.ai/products/{id}'));

        $this->assertSame('http://circle.ai/products/1', Url::createFromTokens($tokens, 'http://circle.ai', $annotation));
    }
}