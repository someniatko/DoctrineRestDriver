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

namespace Circle\DoctrineRestDriver\Types;

use Circle\DoctrineRestDriver\Validation\Assertions;

/**
 * RestClientSecurity type
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 */
class RestClientSecurityOptions {
    use Assertions;

    /**
     * @var array
     */
    private $options;

    /**
     * Security constructor
     *
     * @param string $username
     * @param string $password
     * @param string $strategy
     * @param array  $options
     */
    public function __construct($username, $password, $strategy, array $options) {
        $this->validate($username, $password, $strategy, $options);
        $this->options = $this->format($username, $password, $strategy, $options);
    }

    /**
     * returns all options
     *
     * @return array
     */
    public function all() {
        return $this->options;
    }

    /**
     * returns the formatted options
     *
     * @param  string $username
     * @param  string $password
     * @param  string $strategy
     * @param  array  $options
     * @return array
     */
    private function format($username, $password, $strategy, array $options) {
        return $strategy === 'basic_http' ? $this->basicHttpAuthentication($username, $password, $options) : [];
    }

    /**
     * returns the options enabling basic http authentication
     *
     * @param  string $username
     * @param  string $password
     * @param  array  $options
     * @return array
     */
    private function basicHttpAuthentication($username, $password, array $options) {
        $basicHttpOptions = new BasicHttpOptions($username, $password, $options);
        return $basicHttpOptions->all();
    }

    /**
     * validates the given input
     *
     * @param string      $username
     * @param string|null $password
     * @param string      $strategy
     * @param array       $options
     * @return void
     */
    private function validate($username, $password, $strategy, $options) {
        $this->assertList('options',                     $options);
        $this->assertString('strategy',                  $strategy);
        $this->assertString('username',                  $username);
        $this->assertMaybeString('password',             $password);
        $this->assertList('options[CURLOPT_HTTPHEADER]', $options[CURLOPT_HTTPHEADER]);
    }
}