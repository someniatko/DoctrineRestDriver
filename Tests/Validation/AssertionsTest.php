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

namespace Circle\DoctrineRestDriver\Tests\Validation;

use Circle\DoctrineRestDriver\Security\HttpAuthentication;
use Circle\DoctrineRestDriver\Validation\Assertions;

/**
 * Tests the driver
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 *
 * @coversDefaultClass Circle\DoctrineRestDriver\Validation\Assertions
 * @SuppressWarnings("PHPMD.TooManyPublicMethods")
 */
class AssertionsTest extends \PHPUnit_Framework_TestCase {

    /**
     * @test
     * @group  unit
     * @covers ::assertNotNil
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function assertNotNilTest() {
        $this->assertSame(null, Assertions::assertNotNil('test', 'test'));
    }

    /**
     * @test
     * @group  unit
     * @covers ::assertString
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function assertStringTest() {
        $this->assertSame(null, Assertions::assertString('test', 'test'));
    }

    /**
     * @test
     * @group  unit
     * @covers ::assertHashMap
     * @covers ::assertHashMapEntry
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function assertHashMapTest() {
        $this->assertSame(null, Assertions::assertHashMap('test', [
            'test' => 'test'
        ]));
    }

    /**
     * @test
     * @group  unit
     * @covers ::assertHashMapEntryExists
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function assertHashMapEntryExistsTest() {
        $this->assertSame(null, Assertions::assertHashMapEntryExists('test', [
            'test' => 'test'
        ], 'test'));
    }

    /**
     * @test
     * @group  unit
     * @covers ::assertClassExists
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function assertClassExistsTest() {
        $this->assertSame(null, Assertions::assertClassExists('Circle\DoctrineRestDriver\Tests\Validation\AssertionsTest'));
    }

    /**
     * @test
     * @group  unit
     * @covers ::assertSupportedFetchMode
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function assertSupportedFetchModeTest() {
        $this->assertSame(null, Assertions::assertSupportedFetchMode(\PDO::FETCH_ASSOC));
    }

    /**
     * @test
     * @group  unit
     * @covers ::isUrl
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function isUrlTest() {
        $this->assertTrue(Assertions::isUrl('http://www.circle.ai'));
    }

    /**
     * @test
     * @group  unit
     * @covers ::isUrl
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function isUrlLocalhostTest() {
        $this->assertTrue(Assertions::isUrl('http://localhost:3000'));
        $this->assertTrue(Assertions::isUrl('http://localhost:3000/api?filter=true'));
    }

    /**
     * @test
     * @group  unit
     * @covers ::isUrl
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function isNoUrlTest() {
        $this->assertFalse(Assertions::isUrl('http:/localhost:3000'));
        $this->assertFalse(Assertions::isUrl('localhost:3000'));
        $this->assertFalse(Assertions::isUrl('www.circle.ai'));
        $this->assertFalse(Assertions::isUrl('noUrl'));
        $this->assertFalse(Assertions::isUrl(1));
    }

    /**
     * @test
     * @group  unit
     * @covers ::assertAuthStrategy
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function assertAuthStrategy() {
        $this->assertSame(null, Assertions::assertAuthStrategy(new HttpAuthentication([])));
    }

    /**
     * @test
     * @group  unit
     * @covers ::assertMaybeList
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function assertMaybeList() {
        $this->assertSame(null, Assertions::assertMaybeList('null', null));
        $this->assertSame(null, Assertions::assertMaybeList('list', []));
    }

    /**
     * @test
     * @group  unit
     * @covers ::assertMaybeList
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     * @expectedException \Circle\DoctrineRestDriver\Validation\Exceptions\InvalidTypeException
     */
    public function assertMaybeListOnException() {
        Assertions::assertMaybeList('string', 'hello');
    }

    /**
     * @test
     * @group  unit
     * @covers ::assertMaybeString
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function assertMaybeString() {
        $this->assertSame(null, Assertions::assertMaybeString('null', null));
        $this->assertSame(null, Assertions::assertMaybeString('string', 'value'));
    }

    /**
     * @test
     * @group  unit
     * @covers ::assertMaybeString
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     * @expectedException \Circle\DoctrineRestDriver\Validation\Exceptions\InvalidTypeException
     */
    public function assertMaybeStringOnException() {
        Assertions::assertMaybeString('array', []);
    }

    /**
     * @test
     * @group  unit
     * @covers ::assertMaybeInt
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function assertMaybeInt() {
        $this->assertSame(null, Assertions::assertMaybeInt('null', null));
        $this->assertSame(null, Assertions::assertMaybeInt('int', 123));
    }

    /**
     * @test
     * @group  unit
     * @covers ::assertMaybeInt
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     * @expectedException \Circle\DoctrineRestDriver\Validation\Exceptions\InvalidTypeException
     */
    public function assertMaybeIntOnException() {
        Assertions::assertMaybeInt('float', 3.2);
    }

    /**
     * @test
     * @group  unit
     * @covers ::assertUrl
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function assertUrl() {
        $this->assertSame(null, Assertions::assertUrl('Url', 'http://www.test.com'));
        $this->assertSame(null, Assertions::assertUrl('Url', 'http://www.test.com?filter=1'));
        $this->assertSame(null, Assertions::assertUrl('Url', 'http://circle.ai'));
        $this->assertSame(null, Assertions::assertUrl('Url', 'http://circle.ai/test?test=test'));
    }

    /**
     * @test
     * @group  unit
     * @covers ::assertUrl
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     * @expectedException \Circle\DoctrineRestDriver\Validation\Exceptions\InvalidTypeException
     */
    public function assertUrlOnException() {
        Assertions::assertUrl('Url', 'localhost:3000');
    }
}