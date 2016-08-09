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

use Circle\DoctrineRestDriver\Exceptions\Exceptions;
use Circle\DoctrineRestDriver\Validation\Assertions;
use Circle\DoctrineRestDriver\Validation\Exceptions\InvalidTypeException;

/**
 * HashMapEntry type
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 */
class HashMapEntry {

    /**
     * Asserts if the given value is a hash map entry
     *
     * @param  mixed  $value
     * @param  string $varName
     * @return mixed
     * @throws InvalidTypeException
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public static function assert($value, $varName) {
        if(!is_array($value)) return Exceptions::InvalidTypeException('HashMapEntry', $varName, $value);
        if(count($value) > 1) return Exceptions::InvalidTypeException('HashMapEntry', $varName, $value);

        $keys = array_keys($value);
        $key  = end($keys);

        Assertions::assertString('HashMapEntry of "' . $varName . '": "' . $key . '"', $key);

        return $value;
    }

    /**
     * Asserts if the given hash map entry exists
     *
     * @param  array  $hashMap
     * @param  string $entryName
     * @param  string $varName
     * @return array
     * @throws InvalidTypeException
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public static function assertExists($hashMap, $entryName, $varName) {
        HashMap::assert($hashMap, $varName);
        return array_key_exists($entryName, $hashMap) ? $hashMap : Exceptions::InvalidTypeException('HashMapEntry', $varName . '[\'' . $entryName . '\']', 'undefined');
    }
}