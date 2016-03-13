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

namespace Circle\DoctrineRestDriverBundle\Tests\Factory;

use Circle\DoctrineRestDriverBundle\Factory\RestClientFactory;

/**
 * Tests the restclient factory
 *
 * @author    Tobias Hauck <tobias.hauck@teeage-beatz.de>
 * @copyright 2015 TeeAge-Beatz UG
 *
 * @coversDefaultClass Circle\DoctrineRestDriverBundle\Factory\RestClientFactory
 */
class RestClientFactoryTest extends \PHPUnit_Framework_TestCase {

    /**
     * @test
     * @group  unit
     * @covers ::createOne
     */
    public function complete() {
        $factory           = new RestClientFactory();
        $restClientOptions = $this->getMockBuilder('Circle\DoctrineRestDriverBundle\Types\RestClientOptions')->disableOriginalConstructor()->getMock();
        $restClientOptions
            ->expects($this->once())
            ->method('all')
            ->will($this->returnValue([]));
        $this->assertInstanceOf('Circle\RestClientBundle\Services\RestClient', $factory->createOne($restClientOptions));
    }
}