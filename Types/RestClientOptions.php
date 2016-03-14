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
class RestClientOptions {
    use Assertions;

    /**
     * @var array
     */
    private $defaultOptions = [
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_MAXREDIRS      => 25,
        CURLOPT_TIMEOUT        => 25,
        CURLOPT_CONNECTTIMEOUT => 25,
        CURLOPT_CRLF           => true,
        CURLOPT_SSLVERSION     => 3,
        CURLOPT_FOLLOWLOCATION => true,
    ];

    /**
     * @var array
     */
    private $options;

    /**
     * Request constructor
     *
     * @param array $options
     */
    public function __construct(array $options) {
        $this->validate($options);

        $restClientOptions = $this->format($options['driverOptions']);
        $securityOptions   = new RestClientSecurityOptions($options['user'], $options['password'], $options['driverOptions']['security_strategy'], $restClientOptions);

        $this->options = $securityOptions->all() + $restClientOptions;
    }

    /**
     * returns all rest client options
     *
     * @return array
     */
    public function all() {
        return $this->options;
    }

    /**
     * formats the given options array
     *
     * @param  array $options
     * @return array
     */
    private function format(array $options) {
        return $this->formatHttpHeader($this->convertStringKeysToIntKeys($options));
    }

    /**
     * formats the http header
     *
     * @param  array $options
     * @return array
     */
    private function formatHttpHeader(array $options) {
        $options[CURLOPT_HTTPHEADER] = empty($options[CURLOPT_HTTPHEADER]) ? [] : $options[CURLOPT_HTTPHEADER];
        $options[CURLOPT_HTTPHEADER] = is_string($options[CURLOPT_HTTPHEADER]) ? explode(',', $options[CURLOPT_HTTPHEADER]) : $options[CURLOPT_HTTPHEADER];

        return $options;
    }

    /**
     * converts all string keys to int keys by using php constant() function
     *
     * @param  array $options
     * @return array
     */
    private function convertStringKeysToIntKeys(array $options) {
        $newOptions = [];
        unset($options['security_strategy']);
        foreach ($options as $key => $value) $newOptions[constant($key)] = $value;
        return $newOptions + $this->defaultOptions;
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