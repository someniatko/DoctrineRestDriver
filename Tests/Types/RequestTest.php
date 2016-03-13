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

namespace Circle\DoctrineRestDriverBundle\Tests\Types;

use Circle\DoctrineRestDriverBundle\Types\Request;
use Circle\DoctrineRestDriverBundle\Types\RestClientOptions;

/**
 * Tests the request type
 *
 * @author    Tobias Hauck <tobias.hauck@teeage-beatz.de>
 * @copyright 2015 TeeAge-Beatz UG
 *
 * @coversDefaultClass Circle\DoctrineRestDriverBundle\Types\Request
 */
class RequestTest extends \PHPUnit_Framework_TestCase {

    /**
     * @test
     * @group  unit
     * @covers ::__construct
     * @covers ::getMethod
     * @covers ::getUrl
     * @covers ::getPayload
     * @covers ::__toString
     */
    public function complete() {
        $request = new Request('GET', 'http://circle.ai', 'genious=1');
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('http://circle.ai?genious=1', $request->getUrl());
        $this->assertSame(null, $request->getPayload());
        $this->assertSame('GET http://circle.ai?genious=1 HTTP/1.1', $request->__toString());
    }
}