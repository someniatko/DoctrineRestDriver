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
 * RestClientOptions type
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 */
class RestClientOptions extends \ArrayObject {
    use Assertions;

    /**
     * Request constructor
     *
     * @param array $options
     */
    public function __construct(array $options) {
        $this->validate($options);

        $user          = $options['user'];
        $password      = $options['password'];
        $driverOptions = $options['driverOptions'];
        $strategy      = $driverOptions['security_strategy'];

        $nonCurlOptions  = (array) new NonCurlOptions($driverOptions);
        $curlOptions     = (array) new CurlOptions($driverOptions);
        $securityOptions = (array) new SecurityOptions($user, $password, $strategy, $curlOptions, $nonCurlOptions);

        parent::__construct($securityOptions + $curlOptions);
    }

    /**
     * validates the given input
     *
     * @param  array $params
     * @return void
     */
    private function validate(array $params) {
        $this->assertHashMap('params', $params);
        $this->assertHashMapEntryExists('params', $params, 'driverOptions');
        $this->assertHashMap('params["driverOptions"]', $params['driverOptions']);
        $this->assertHashMapEntryExists('params["driverOptions"]', $params['driverOptions'], 'security_strategy');
        $this->assertString('params["driverOptions"]["security_strategy"]', $params['driverOptions']['security_strategy']);
        $this->assertHashMapEntryExists('params', $params, 'user');
        $this->assertString('params["user"]', $params['user']);
        $this->assertHashMapEntryExists('params', $params, 'password');
        $this->assertMaybeString('params["password"]', $params['password']);
    }
}