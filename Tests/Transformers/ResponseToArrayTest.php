<?php
/**
 * This file is part of DoctrineRestDriverBundle.
 *
 * DoctrineRestDriverBundle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * DoctrineRestDriverBundle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with DoctrineRestDriverBundle.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Circle\DoctrineRestDriverBundle\Tests\Transformers;

use Circle\DoctrineRestDriverBundle\Transformers\ResponseToArray;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests the response to array transformer
 *
 * @author    Tobias Hauck <tobias.hauck@teeage-beatz.de>
 * @copyright 2015 TeeAge-Beatz UG
 *
 * @coversDefaultClass Circle\DoctrineRestDriverBundle\Transformers\ResponseToArray
 */
class ResponseToArrayTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var ResponseToArray
     */
    private $responseToArray;

    /**
     * {@inheritdoc}
     */
    public function setUp() {
        $this->responseToArray = new ResponseToArray();
    }

    /**
     * @test
     * @group  unit
     * @covers ::__construct
     * @covers ::trans
     * @covers ::<private>
     */
    public function delete() {
        $query    = 'DELETE FROM product WHERE id=1';
        $response = new Response();
        $expected = [];

        $this->assertEquals($expected, $this->responseToArray->trans($response, $query));
    }

    /**
     * @test
     * @group  unit
     * @covers ::__construct
     * @covers ::trans
     * @covers ::<private>
     */
    public function insert() {
        $query    = 'INSERT INTO product (name, value) VALUES ("myName", "myValue")';
        $response = new Response(json_encode([
            'id'    => 1,
            'name'  => 'myName',
            'value' => 'myValue'
        ]));
        $expected = [
            'id'    => 1,
            'name'  => 'myName',
            'value' => 'myValue'
        ];

        $this->assertEquals($expected, $this->responseToArray->trans($response, $query));
    }

    /**
     * @test
     * @group  unit
     * @covers ::__construct
     * @covers ::trans
     * @covers ::<private>
     */
    public function update() {
        $query    = 'UPDATE product SET name="newName", value="newValue" WHERE id=1';
        $response = new Response(json_encode([
            'id'    => 1,
            'name'  => 'newName',
            'value' => 'newValue'
        ]));
        $expected = [
            'id'    => 1,
            'name'  => 'newName',
            'value' => 'newValue'
        ];

        $this->assertEquals($expected, $this->responseToArray->trans($response, $query));
    }

    /**
     * @test
     * @group  unit
     * @covers ::__construct
     * @covers ::trans
     * @covers ::<private>
     */
    public function selectOneWithoutAlias() {
        $query    = 'SELECT name FROM product WHERE id=1';
        $response = new Response(json_encode([
            'id'    => 1,
            'name'  => 'myName',
            'value' => 'myValue'
        ]));
        $expected = [
            [
                'name'  => 'myName'
            ]
        ];

        $this->assertEquals($expected, $this->responseToArray->trans($response, $query));
    }

    /**
     * @test
     * @group  unit
     * @covers ::__construct
     * @covers ::trans
     * @covers ::<private>
     */
    public function selectOneWithAlias() {
        $query    = 'SELECT p.name, p.value FROM product p WHERE p.id=1';
        $response = new Response(json_encode([
            'id'    => 1,
            'name'  => 'myName',
            'value' => 'myValue'
        ]));
        $expected = [
            [
                'p.name'  => 'myName',
                'p.value' => 'myValue'
            ]
        ];

        $this->assertEquals($expected, $this->responseToArray->trans($response, $query));
    }

    /**
     * @test
     * @group  unit
     * @covers ::__construct
     * @covers ::trans
     * @covers ::<private>
     */
    public function selectMultiple() {
        $query    = 'SELECT p.name, p.value FROM product p';
        $response = new Response(json_encode([
            [
                'id'    => 1,
                'name'  => 'myName',
                'value' => 'myValue'
            ],
            [
                'id'    => 2,
                'name'  => 'anotherName',
                'value' => 'anotherValue'
            ]
        ]));
        $expected = [
            [
                'p.name'  => 'myName',
                'p.value' => 'myValue'
            ],
            [
                'p.name'  => 'anotherName',
                'p.value' => 'anotherValue'
            ]
        ];

        $this->assertEquals($expected, $this->responseToArray->trans($response, $query));
    }

    /**
     * @test
     * @group  unit
     * @covers ::__construct
     * @covers ::trans
     * @covers ::<private>
     */
    public function selectMultipleOrderByName() {
        $query    = 'SELECT p.name, p.value FROM product p order by p.name ASC, p.value DESC';
        $response = new Response(json_encode([
            [
                'id'    => 1,
                'name'  => 'myName',
                'value' => 'myValue'
            ],
            [
                'id'    => 2,
                'name'  => 'anotherName',
                'value' => 'anotherValue'
            ],
            [
                'id'    => 3,
                'name'  => 'sameName',
                'value' => 'firstValue'
            ],
            [
                'id'    => 4,
                'name'  => 'sameName',
                'value' => 'lastValue'
            ]
        ]));
        $expected = [
            [
                'p.name'  => 'anotherName',
                'p.value' => 'anotherValue'
            ],
            [
                'p.name'  => 'myName',
                'p.value' => 'myValue'
            ],
            [
                'p.name'  => 'sameName',
                'p.value' => 'lastValue'
            ],
            [
                'p.name'  => 'sameName',
                'p.value' => 'firstValue'
            ]
        ];

        $this->assertEquals($expected, $this->responseToArray->trans($response, $query));
    }
}