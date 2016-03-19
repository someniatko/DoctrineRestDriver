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
 * CurlOptions type
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 */
class CurlOptions extends \ArrayObject {

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
     * Request constructor
     *
     * @param array $options
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function __construct(array $options) {
        Assertions::assertHashMap('options', $options);

        $curlOptions = array_filter($options, function($key) {
            return preg_match('/^CURLOPT_/', $key);
        }, ARRAY_FILTER_USE_KEY);

        parent::__construct($this->formatHttpHeader($this->resolveConstants($curlOptions)) + $this->defaultOptions);
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
    private function resolveConstants(array $options) {
        $keys = array_map(function($key) {
            return constant($key);
        }, array_keys($options));

        return array_combine($keys, array_values($options));
    }
}