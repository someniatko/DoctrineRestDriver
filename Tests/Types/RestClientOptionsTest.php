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

use Circle\DoctrineRestDriver\Types\RestClientOptions;

/**
 * Tests the rest client options
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 *
 * @coversDefaultClass Circle\DoctrineRestDriver\Types\RestClientOptions
 */
class RestClientOptionsTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var array
     */
    private $options;

    /**
     * @var array
     */
    private $expected;

    /**
     * {@inheritdoc}
     */
    public function setUp() {
        $this->options = [
            'driver_class'  => 'Circle\DoctrineRestDriver\Driver',
            'host'          => 'http://www.circle.ai',
            'port'          => '8080',
            'dbname'        => 'circle',
            'user'          => 'circleUser',
            'password'      => 'mySecretPassword',
            'charset'       => 'UTF8',
            'driverOptions' => [
                'security_strategy'  => 'none',
                'CURLOPT_MAXREDIRS'  => 22,
                'CURLOPT_HTTPHEADER' => 'Content-Type: text/plain'
            ]
        ];

        $this->expected = [
            CURLOPT_HTTPHEADER     => ['Content-Type: text/plain'],
            CURLOPT_MAXREDIRS      => 22,
            CURLOPT_TIMEOUT        => 25,
            CURLOPT_CONNECTTIMEOUT => 25,
            CURLOPT_CRLF           => true,
            CURLOPT_SSLVERSION     => 3,
            CURLOPT_FOLLOWLOCATION => true,
        ];
    }

    /**
     * @test
     * @group  unit
     * @covers ::__construct
     * @covers ::<private>
     * @covers \Circle\DoctrineRestDriver\Types\SecurityOptions::__construct
     * @covers \Circle\DoctrineRestDriver\Types\SecurityOptions::<private>
     */
    public function cast() {
        $options  = new RestClientOptions($this->options);
        $expected = $this->expected;

        $this->assertEquals($expected, (array) $options);
    }

    /**
     * @test
     * @group unit
     * @covers ::__construct
     * @covers ::<private>
     * @covers \Circle\DoctrineRestDriver\Types\SecurityOptions::__construct
     * @covers \Circle\DoctrineRestDriver\Types\SecurityOptions::<private>
     */
    public function castWithBasicHttpAuth() {
        $options                                       = $this->options;
        $options['driverOptions']['security_strategy'] = 'basic_http';
        $options                                       = new RestClientOptions($options);
        $expected                                      = $this->expected;

        $expected[CURLOPT_HTTPHEADER] = [
            'Content-Type: text/plain',
            'Authorization: Basic Y2lyY2xlVXNlcjpteVNlY3JldFBhc3N3b3Jk'
        ];

        $this->assertEquals($expected, (array) $options);
    }

    /**
     * @test
     * @group unit
     * @covers ::__construct
     * @covers ::<private>
     * @covers \Circle\DoctrineRestDriver\Types\SecurityOptions::__construct
     * @covers \Circle\DoctrineRestDriver\Types\SecurityOptions::<private>
     */
    public function castWithOAuthTwitter() {
        $options                                         = $this->options;
        $options['driverOptions']['security_strategy']   = 'oauth';
        $options['driverOptions']['oauth_options_class'] = 'Circle\DoctrineRestDriver\Tests\Types\Stubs\TwitterOAuthStub';
        $options                                         = new RestClientOptions($options);
        $expected                                        = $this->expected;

        $expected[CURLOPT_HTTPHEADER] = [
            'Content-Type: text/plain',
            'Authorization: Bearer AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'
        ];

        $this->assertEquals($expected, (array) $options);
    }
}