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

namespace Circle\DoctrineRestDriver\Tests\Mapping;

use Circle\DoctrineRestDriver\Mapping\ResultMapping;
use PHPSQLParser\PHPSQLParser;

/**
 * Tests the result mapping
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 *
 * @coversDefaultClass Circle\DoctrineRestDriver\Mapping\ResultMapping
 */
class ResultMappingTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var ResultMapping
     */
    private $resultMapping;

    /**
     * {@inheritdoc}
     */
    public function setUp() {
        $this->resultMapping = new ResultMapping();
    }

    /**
     * @test
     * @group  unit
     * @covers ::select
     * @covers ::<private>
     */
    public function select() {
        $query  = 'SELECT name FROM products WHERE id=1';
        $parser = new PHPSQLParser();
        $tokens = $parser->parse($query);

        $content = [
            'name' => 'username'
        ];

        $expected = [
            [
                'name' => 'username'
            ]
        ];

        $this->assertEquals($expected, $this->resultMapping->select($tokens, $content));
    }

    /**
     * @test
     * @group  unit
     * @covers ::selectSingle
     * @covers ::<private>
     */
    public function selectSingle() {
        $query  = 'SELECT name FROM products WHERE id=1';
        $parser = new PHPSQLParser();
        $tokens = $parser->parse($query);

        $content = [
            'name' => 'username'
        ];

        $expected = [
            [
                'name' => 'username'
            ]
        ];

        $this->assertEquals($expected, $this->resultMapping->selectSingle($tokens, $content));
    }

    /**
     * @test
     * @group  unit
     * @covers ::selectAll
     * @covers ::<private>
     */
    public function selectAll() {
        $query  = 'SELECT name FROM products';
        $parser = new PHPSQLParser();
        $tokens = $parser->parse($query);

        $content = [
            [
                'name' => 'username'
            ],
            [
                'name' => 'anotherUser'
            ],
        ];

        $expected = [
            [
                'name' => 'username'
            ],
            [
                'name' => 'anotherUser'
            ],
        ];

        $this->assertEquals($expected, $this->resultMapping->selectAll($tokens, $content));
    }

    /**
     * @test
     * @group  unit
     * @covers ::selectAll
     * @covers ::<private>
     */
    public function selectAllOrderBy() {
        $query  = 'SELECT name FROM products ORDER BY name';
        $parser = new PHPSQLParser();
        $tokens = $parser->parse($query);

        $content = [
            [
                'name' => 'username'
            ],
            [
                'name' => 'anotherUser'
            ],
        ];

        $expected = [
            [
                'name' => 'anotherUser'
            ],
            [
                'name' => 'username'
            ],
        ];

        $this->assertEquals($expected, $this->resultMapping->selectAll($tokens, $content));
    }

    /**
     * @test
     * @group  unit
     * @covers ::delete
     * @covers ::<private>
     */
    public function delete() {
        $this->assertEquals([], $this->resultMapping->delete());
    }

    /**
     * @test
     * @group  unit
     * @covers ::insert
     * @covers ::<private>
     */
    public function insert() {
        $content = [
            [
                'name' => 'username'
            ]
        ];

        $this->assertEquals($content, $this->resultMapping->insert($content));
    }

    /**
     * @test
     * @group  unit
     * @covers ::update
     * @covers ::<private>
     */
    public function update() {
        $content = [
            [
                'name' => 'username'
            ]
        ];

        $this->assertEquals($content, $this->resultMapping->update($content));
    }
}