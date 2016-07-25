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

namespace Circle\DoctrineRestDriver\Tests\Annotations;

use Circle\DoctrineRestDriver\Annotations\Delete;
use Circle\DoctrineRestDriver\Annotations\Select;
use Circle\DoctrineRestDriver\Annotations\Fetch;
use Circle\DoctrineRestDriver\Annotations\Insert;
use Circle\DoctrineRestDriver\Annotations\Update;
use Circle\DoctrineRestDriver\Annotations\Routing;

/**
 * Tests the routing bag
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 *
 * @coversDefaultClass Circle\DoctrineRestDriver\Annotations\Routing
 */
class RoutingTest extends \PHPUnit_Framework_TestCase {

    /**
     * @test
     * @group  unit
     * @covers ::__construct
     * @covers ::post
     */
    public function post() {
        $url  = 'http://www.mySite.com/post';
        $post = new Insert([
            'value' => $url
        ]);

        $routing = new Routing(['post' => $post]);

        $this->assertSame($url, $routing->post());
    }

    /**
     * @test
     * @group  unit
     * @covers ::__construct
     * @covers ::put
     */
    public function put() {
        $url = 'http://www.mySite.com/put';
        $put = new Update([
            'value' => $url
        ]);

        $routing = new Routing(['put' => $put]);

        $this->assertSame($url, $routing->put());
    }

    /**
     * @test
     * @group  unit
     * @covers ::__construct
     * @covers ::get
     */
    public function get() {
        $url = 'http://www.mySite.com/get';
        $get = new Select([
            'value' => $url
        ]);

        $routing = new Routing(['get' => $get]);

        $this->assertSame($url, $routing->get());
    }

    /**
     * @test
     * @group  unit
     * @covers ::__construct
     * @covers ::delete
     */
    public function delete() {
        $url    = 'http://www.mySite.com/delete';
        $delete = new Delete([
            'value' => $url
        ]);

        $routing = new Routing(['delete' => $delete]);

        $this->assertSame($url, $routing->delete());
    }

    /**
     * @test
     * @group  unit
     * @covers ::__construct
     * @covers ::getAll
     */
    public function getAll() {
        $url    = 'http://www.mySite.com/getAll';
        $getAll = new Fetch([
            'value' => $url
        ]);

        $routing = new Routing(['getAll' => $getAll]);

        $this->assertSame($url, $routing->getAll());
    }
}