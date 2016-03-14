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

use Circle\DoctrineRestDriver\Types\BasicHttpOptions;

/**
 * Tests the basic http options type
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 *
 * @coversDefaultClass Circle\DoctrineRestDriver\Types\BasicHttpOptions
 */
class BasicHttpOptionsTest extends \PHPUnit_Framework_TestCase {

    /**
     * @test
     * @group  unit
     * @covers ::__construct
     * @covers ::all
     * @covers ::<private>
     */
    public function complete() {
        $options = new BasicHttpOptions('user', 'password', [
            CURLOPT_HTTPHEADER => ['Content-Type: application/json']
        ]);

        $expected = [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Basic dXNlcjpwYXNzd29yZA=='
            ]
        ];

        $this->assertSame($expected, $options->all());
    }
}