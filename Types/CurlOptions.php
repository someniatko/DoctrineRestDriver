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
    private static $defaults = [
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_MAXREDIRS      => 25,
        CURLOPT_TIMEOUT        => 25,
        CURLOPT_CONNECTTIMEOUT => 25,
        CURLOPT_CRLF           => true,
        CURLOPT_SSLVERSION     => 3,
        CURLOPT_FOLLOWLOCATION => true,
    ];

    /**
     * returns valid curl options from the given options array
     *
     * @param  array $options
     * @return array
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public static function create(array $options) {
        Assertions::assertHashMap('options', $options);

        $filteredOptions = array_filter($options, function($key) {
            return preg_match('/^CURLOPT_/', $key);
        }, ARRAY_FILTER_USE_KEY);

        $keys = array_map(function($key) {
            return constant($key);
        }, array_keys($filteredOptions));

        $optionsWithIntKeys = array_combine($keys, array_values($filteredOptions));

        $optionsWithIntKeys[CURLOPT_HTTPHEADER] = empty($optionsWithIntKeys[CURLOPT_HTTPHEADER]) ? [] : $optionsWithIntKeys[CURLOPT_HTTPHEADER];
        $optionsWithIntKeys[CURLOPT_HTTPHEADER] = is_string($optionsWithIntKeys[CURLOPT_HTTPHEADER]) ? explode(',', $optionsWithIntKeys[CURLOPT_HTTPHEADER]) : $optionsWithIntKeys[CURLOPT_HTTPHEADER];

        return $optionsWithIntKeys + self::$defaults;
    }
}