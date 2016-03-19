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

namespace Circle\DoctrineRestDriver\Validation;

use Circle\DoctrineRestDriver\Exceptions\UnsupportedFetchModeException;
use Circle\DoctrineRestDriver\Validation\Exceptions\InvalidTypeException;
use Circle\DoctrineRestDriver\Validation\Exceptions\NotNilException;
use Circle\DoctrineRestDriver\Exceptions\Exceptions;
use Prophecy\Exception\Doubler\ClassNotFoundException;

/**
 * Trait including assertions
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 *
 * @SuppressWarnings("PHPMD.StaticAccess")
 */
class Assertions {

    /**
     * asserts if the given value is not nil
     *
     * @param  string     $varName
     * @param  mixed      $value
     * @return null
     * @throws NotNilException
     */
    public static function assertNotNil($varName, $value) {
        return $value === null ? Exceptions::notNilException($varName) : null;
    }

    /**
     * asserts if the given value is a string
     *
     * @param  string     $varName
     * @param  mixed      $value
     * @return null
     * @throws InvalidTypeException
     * @throws NotNilException
     */
    public static function assertString($varName, $value) {
        self::assertNotNil($varName, $value);
        return !is_string($value) ? Exceptions::invalidTypeException('string', $varName, $value) : null;
    }

    /**
     * asserts if the given value is a hash map
     *
     * @param  string     $varName
     * @param  mixed      $value
     * @return void
     * @throws InvalidTypeException
     */
    public static function assertHashMap($varName, $value) {
        if(!is_array($value)) return Exceptions::invalidTypeException('HashMap', $varName, $value);
        foreach($value as $key => $v) self::assertHashMapEntry($varName, [$key => $v]);
    }

    /**
     * asserts if the given value is a hash map entry
     *
     * @param  string      $varName
     * @param  mixed       $value
     * @return void
     * @throws InvalidTypeException
     */
    public static function assertHashMapEntry($varName, $value) {
        if(!is_array($value)) return Exceptions::invalidTypeException('HashMapEntry', $varName, $value);
        if(count($value) > 1) return Exceptions::invalidTypeException('HashMapEntry', $varName, $value);

        $keys = array_keys($value);
        $key  = end($keys);
        self::assertString('HashMapEntry of "' . $varName . '": "' . $key . '"', $key);
    }

    /**
     * asserts if the given value is a hash map entry
     *
     * @param  string      $varName
     * @param  array       $hashMap
     * @param  string      $entryName
     * @return null
     * @throws InvalidTypeException
     */
    public static function assertHashMapEntryExists($varName, $hashMap, $entryName) {
        self::assertHashMap($varName, $hashMap);
        return array_key_exists($entryName, $hashMap) ? null : Exceptions::invalidTypeException('HashMapEntry', $varName . '[\'' . $entryName . '\']', 'undefined');
    }

    /**
     * checks if the given value is a url
     *
     * @param  mixed $value
     * @return bool
     */
    public static function isUrl($value) {
        return (bool) (preg_match('/^(http|ftp|https):\/\/[0-9a-zA-Z_-]+(\.[0-9a-zA-Z_-]+)+([0-9a-zA-Z_\-.,@?^=%&amp;:\/~+#-]*[0-9a-zA-Z_\-@?^=%&amp;\/~+#-])?/', $value));
    }

    /**
     * checks if the given fetch mode is supported
     *
     * @param  int $fetchMode
     * @return null
     * @throws UnsupportedFetchModeException
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public static function assertSupportedFetchMode($fetchMode) {
        return $fetchMode !== \PDO::FETCH_ASSOC ? Exceptions::unsupportedFetchModeException($fetchMode) : null;
    }

    /**
     * checks if the given class exists
     *
     * @param  string     $className
     * @return Assertions
     * @throws ClassNotFoundException
     */
    public static function assertClassExists($className) {
        if (!empty($className) && !class_exists($className)) throw new ClassNotFoundException('Class not found', $className);
    }
}