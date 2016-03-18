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

namespace Circle\DoctrineRestDriver\Tests\Transformers;

use Circle\DoctrineRestDriver\Transformers\MysqlToRequest;
use Circle\DoctrineRestDriver\Types\Request;

/**
 * Tests the mysql to request transformer
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 *
 * @coversDefaultClass Circle\DoctrineRestDriver\Transformers\MysqlToRequest
 */
class MysqlToRequestTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var MysqlToRequest
     */
    private $mysqlToRequest;

    /**
     * @var string
     */
    private $apiUrl = 'http://www.test.de';

    /**
     * {@inheritdoc}
     */
    public function setUp() {
        $this->mysqlToRequest = new MysqlToRequest($this->apiUrl);
    }

    /**
     * @test
     * @group  unit
     * @covers ::__construct
     * @covers ::transform
     * @covers ::<private>
     */
    public function selectOne() {
        $query  = 'SELECT name FROM products WHERE id = ?';
        $params = [
            1
        ];
        $expected = new Request('get', $this->apiUrl . '/products/1', null, null);

        $this->assertEquals($expected, $this->mysqlToRequest->transform($query, $params));
    }

    /**
     * @test
     * @group  unit
     * @covers ::__construct
     * @covers ::transform
     * @covers ::<private>
     */
    public function selectOneBy() {
        $query  = 'SELECT name FROM products WHERE id=? AND name=?';
        $params = [
            1,
            'myName'
        ];
        $expected = new Request('get', $this->apiUrl . '/products/1', 'name=myName', null);

        $this->assertEquals($expected, $this->mysqlToRequest->transform($query, $params));
    }

    /**
     * @test
     * @group  unit
     * @covers ::__construct
     * @covers ::transform
     * @covers ::<private>
     */
    public function selectBy() {
        $query  = 'SELECT name FROM products WHERE name=?';
        $params = [
            'myName'
        ];
        $expected = new Request('get', $this->apiUrl . '/products', 'name=myName', null);

        $this->assertEquals($expected, $this->mysqlToRequest->transform($query, $params));
    }

    /**
     * @test
     * @group  unit
     * @covers ::__construct
     * @covers ::transform
     * @covers ::<private>
     */
    public function selectAll() {
        $query    = 'SELECT name FROM products';
        $expected = new Request('get', $this->apiUrl . '/products', null, null);

        $this->assertEquals($expected, $this->mysqlToRequest->transform($query));
    }

    /**
     * @test
     * @group  unit
     * @covers ::__construct
     * @covers ::transform
     * @covers ::<private>
     */
    public function selectJoined() {
        $query    = 'SELECT p.name FROM products p JOIN product.categories c ON c.id = p.categories_id';
        $expected = new Request('get', $this->apiUrl . '/products', null, null);

        $this->assertEquals($expected, $this->mysqlToRequest->transform($query));
    }

    /**
     * @test
     * @group  unit
     * @covers ::__construct
     * @covers ::transform
     * @covers ::<private>
     */
    public function insert() {
        $query    = 'INSERT INTO products ("name") VALUES ("myName")';
        $expected = new Request('post', $this->apiUrl . '/products', null, json_encode([
            'name' => 'myName'
        ]));

        $this->assertEquals($expected, str_replace('\\"', '', $this->mysqlToRequest->transform($query)));
    }

    /**
     * @test
     * @group  unit
     * @covers ::__construct
     * @covers ::transform
     * @covers ::<private>
     */
    public function update() {
        $query  = 'UPDATE products SET name="myValue" WHERE id=?';
        $params = [
            1
        ];
        $expected = new Request('put', $this->apiUrl . '/products/1', null, json_encode([
            'name' => 'myValue'
        ]));

        $this->assertEquals($expected, str_replace('\\"', '', $this->mysqlToRequest->transform($query, $params)));
    }

    /**
     * @test
     * @group  unit
     * @covers ::__construct
     * @covers ::transform
     * @covers ::<private>
     */
    public function updateAll() {
        $query    = 'UPDATE products SET name="myValue"';
        $expected = new Request('put', $this->apiUrl . '/products', null, json_encode([
            'name' => 'myValue'
        ]));

        $this->assertEquals($expected, str_replace('\\"', '', $this->mysqlToRequest->transform($query)));
    }

    /**
     * @test
     * @group  unit
     * @covers ::__construct
     * @covers ::transform
     * @covers ::<private>
     */
    public function delete() {
        $query  = 'DELETE FROM products WHERE id=?';
        $params = [
            1
        ];
        $expected = new Request('delete', $this->apiUrl . '/products/1', null, null);

        $this->assertEquals($expected, $this->mysqlToRequest->transform($query, $params));
    }

    /**
     * @test
     * @group  unit
     * @covers ::__construct
     * @covers ::transform
     * @covers ::<private>
     * @expectedException \Exception
     */
    public function brokenQuery() {
        $query  = 'SHIT products WHERE dirt=?';
        $params = [
            1
        ];
        $this->mysqlToRequest->transform($query, $params);
    }
}