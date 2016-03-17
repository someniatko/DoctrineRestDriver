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
class SecurityOptions extends \ArrayObject {
    use Assertions;

    /**
     * Security constructor
     *
     * @param string $username
     * @param string $password
     * @param string $strategy
     * @param array  $options
     * @param array  $nonCurlOptions
     */
    public function __construct($username, $password, $strategy, array $options, array $nonCurlOptions = array()) {
        $this->validate($username, $password, $strategy, $options);

        parent::__construct($this->format($username, $password, $strategy, $options, $nonCurlOptions));
    }

    /**
     * returns the formatted options
     *
     * @param  string $username
     * @param  string $password
     * @param  string $strategy
     * @param  array  $options
     * @param  array  $nonCurlOptions
     * @return array
     */
    private function format($username, $password, $strategy, array $options, $nonCurlOptions) {
        if ($strategy === 'basic_http') return (array) new BasicHttpOptions($username, $password, $options);
        return $strategy === 'oauth' ? $this->oAuthAuthentication($username, $password, $options, $nonCurlOptions) : [];
    }

    /**
     * returns the options enabling basic http authentication
     *
     * @param  string $username
     * @param  string $password
     * @param  array  $options
     * @param  array  $nonCurlOptions
     * @return array
     */
    private function oAuthAuthentication($username, $password, array $options, array $nonCurlOptions) {
        $oAuthClass = $nonCurlOptions['oauth_options_class'];
        return (array) new $oAuthClass($username, $password, $options);
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