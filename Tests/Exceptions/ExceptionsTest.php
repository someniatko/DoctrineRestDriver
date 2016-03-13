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

namespace Circle\DoctrineRestDriverBundle\Tests\Exceptions;

use Circle\DoctrineRestDriverBundle\Exceptions\Exceptions;

/**
 * Tests the exceptions trait
 *
 * @author    Tobias Hauck <tobias.hauck@teeage-beatz.de>
 * @copyright 2015 TeeAge-Beatz UG
 *
 * @coversDefaultClass Circle\DoctrineRestDriverBundle\Exceptions\Exceptions
 */
class ExceptionsTest extends \PHPUnit_Framework_TestCase {
    use Exceptions;

    /**
     * @test
     * @group  unit
     * @covers ::invalidTypeException
     * @expectedException \Circle\DoctrineRestDriverBundle\Validation\Exceptions\InvalidTypeException
     */
    public function invalidTypeExceptionTest() {
        $this->invalidTypeException('expected', 'key', 'value');
    }

    /**
     * @test
     * @group  unit
     * @covers ::notNilException
     * @expectedException \Circle\DoctrineRestDriverBundle\Validation\Exceptions\NotNilException
     */
    public function notNilExceptionTest() {
        $this->notNilException('test');
    }
}