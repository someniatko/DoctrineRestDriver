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

use Circle\DoctrineRestDriver\Types\Result;

/**
 * Tests the result type
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 *
 * @coversDefaultClass Circle\DoctrineRestDriver\Types\Result
 */
class ResultTest extends \PHPUnit_Framework_TestCase {

    /**
     * @test
     * @group  unit
     * @covers ::get
     * @covers ::<private>
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function getWithSelect() {
        $content = [
            'name' => 'testname'
        ];

        $expected = [
            [
                'name' => 'testname'
            ]
        ];

        $this->assertEquals($expected, (new Result('SELECT name FROM products WHERE id=1', $content))->get());
    }

    /**
     * @test
     * @group  unit
     * @covers ::get
     * @covers ::<private>
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function getWithDelete() {
        $content = [
            'name' => 'testname'
        ];

        $expected = [];

        $this->assertEquals($expected, (new Result('DELETE FROM products WHERE id=1', $content))->get());
    }

    /**
     * @test
     * @group  unit
     * @covers ::get
     * @covers ::<private>
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function getWithInsert() {
        $content = [
            'name' => 'testname'
        ];

        $expected = [
            'name' => 'testname'
        ];

        $this->assertEquals($expected, (new Result('INSERT INTO products (name) VALUES ("testname")', $content))->get());
    }

    /**
     * @test
     * @group  unit
     * @covers ::get
     * @covers ::<private>
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function getWithUpdate() {
        $content = [
            'name' => 'testname'
        ];

        $expected = [
            'name' => 'testname'
        ];

        $this->assertEquals($expected, (new Result('UPDATE products SET name = "testname" WHERE id=1', $content))->get());
    }
}