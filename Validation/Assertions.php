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

use Circle\DoctrineRestDriver\Exceptions\InvalidAuthStrategyException;
use Circle\DoctrineRestDriver\Exceptions\UnsupportedFetchModeException;
use Circle\DoctrineRestDriver\Formatters\Formatter;
use Circle\DoctrineRestDriver\Security\AuthStrategy;
use Circle\DoctrineRestDriver\Validation\Exceptions\InvalidTypeException;
use Circle\DoctrineRestDriver\Validation\Exceptions\NotNilException;
use Circle\DoctrineRestDriver\Exceptions\Exceptions;
use Prophecy\Exception\Doubler\ClassNotFoundException;

/**
 * Assertions
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 *
 * @SuppressWarnings("PHPMD.StaticAccess")
 * @SuppressWarnings("PHPMD.TooManyPublicMethods")
 */
class Assertions {

    /**
     * Asserts if the given value is not nil
     *
     * @param  string $varName
     * @param  mixed  $value
     * @return mixed
     * @throws NotNilException
     */
    public static function assertNotNil($varName, $value) {
        return $value === null ? Exceptions::NotNilException($varName) : $value;
    }

    /**
     * Asserts if the given value is a string
     *
     * @param  string $varName
     * @param  mixed  $value
     * @return mixed
     * @throws InvalidTypeException
     * @throws NotNilException
     */
    public static function assertString($varName, $value) {
        self::assertNotNil($varName, $value);
        return !is_string($value) ? Exceptions::InvalidTypeException('string', $varName, $value) : $value;
    }

    /**
     * Asserts if the given value is a maybe list
     *
     * @param  string $varName
     * @param  mixed  $value
     * @return array|null
     * @throws InvalidTypeException
     * @throws NotNilException
     */
    public static function assertMaybeList($varName, $value) {
        return !is_array($value) && $value !== null ? Exceptions::InvalidTypeException('MaybeList', $varName, $value) : $value;
    }

    /**
     * Asserts if the given value is a maybe string
     *
     * @param  string $varName
     * @param  mixed  $value
     * @return string|null
     * @throws InvalidTypeException
     * @throws NotNilException
     */
    public static function assertMaybeString($varName, $value) {
        return !is_string($value) && $value !== null ? Exceptions::InvalidTypeException('MaybeString', $varName, $value) : $value;
    }

    /**
     * Asserts if the given value is a maybe int
     *
     * @param  string $varName
     * @param  mixed  $value
     * @return int|null
     * @throws InvalidTypeException
     * @throws NotNilException
     */
    public static function assertMaybeInt($varName, $value) {
        return !is_int($value) && $value !== null ? Exceptions::InvalidTypeException('MaybeInt', $varName, $value) : $value;
    }

    /**
     * Asserts if the given value is a hash map
     *
     * @param  string $varName
     * @param  mixed  $value
     * @return void
     * @throws InvalidTypeException
     */
    public static function assertHashMap($varName, $value) {
        if(!is_array($value)) return Exceptions::InvalidTypeException('HashMap', $varName, $value);
        foreach($value as $key => $v) self::assertHashMapEntry($varName, [$key => $v]);

        return $value;
    }

    /**
     * Asserts if the given value is a hash map entry
     *
     * @param  string $varName
     * @param  mixed  $value
     * @return void
     * @throws InvalidTypeException
     */
    public static function assertHashMapEntry($varName, $value) {
        if(!is_array($value)) return Exceptions::InvalidTypeException('HashMapEntry', $varName, $value);
        if(count($value) > 1) return Exceptions::InvalidTypeException('HashMapEntry', $varName, $value);

        $keys = array_keys($value);
        $key  = end($keys);
        self::assertString('HashMapEntry of "' . $varName . '": "' . $key . '"', $key);
    }

    /**
     * Asserts if the given hash map entry exists
     *
     * @param  string $varName
     * @param  array  $hashMap
     * @param  string $entryName
     * @return null
     * @throws InvalidTypeException
     */
    public static function assertHashMapEntryExists($varName, $hashMap, $entryName) {
        self::assertHashMap($varName, $hashMap);
        return array_key_exists($entryName, $hashMap) ? $hashMap : Exceptions::InvalidTypeException('HashMapEntry', $varName . '[\'' . $entryName . '\']', 'undefined');
    }

    /**
     * Asserts if the given value is an url
     *
     * @param  string $varName
     * @param  mixed  $value
     * @return string
     * @throws InvalidTypeException
     */
    public static function assertUrl($varName, $value) {
        return !self::isUrl($value) ? Exceptions::InvalidTypeException('Url', $varName, $value) : $value;
    }

    /**
     * Checks if the given value is a url
     *
     * @param  mixed $value
     * @return bool
     */
    public static function isUrl($value) {
        return (bool) (preg_match('/^(http|ftp|https):\/\/([0-9a-zA-Z_-]+(\.[0-9a-zA-Z_-]+)+|localhost)([0-9a-zA-Z_\-.,@?^=%&amp;:\/~+#-]*[0-9a-zA-Z_\-@?^=%&amp;\/~+#-])?/', $value));
    }

    /**
     * Checks if the given fetch mode is supported
     *
     * @param  int  $fetchMode
     * @return null
     * @throws UnsupportedFetchModeException
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public static function assertSupportedFetchMode($fetchMode) {
        return $fetchMode !== \PDO::FETCH_ASSOC ? Exceptions::UnsupportedFetchModeException($fetchMode) : $fetchMode;
    }

    /**
     * Checks if the given class exists
     *
     * @param  string     $className
     * @return void
     * @throws ClassNotFoundException
     */
    public static function assertClassExists($className) {
        if (!empty($className) && !class_exists($className)) throw new ClassNotFoundException('Class not found', $className);
    }

    /**
     * Checks if the given instance is instanceof AuthStrategy
     *
     * @param  object $instance
     * @return null
     * @throws InvalidAuthStrategyException
     */
    public static function assertAuthStrategy($instance) {
        return !$instance instanceof AuthStrategy ? Exceptions::invalidAuthStrategyException(get_class($instance)) : $instance;
    }

    /**
     * Checks if the given instance is instanceof Formatter
     *
     * @param  object $instance
     * @return null
     * @throws InvalidAuthStrategyException
     */
    public static function assertFormatter($instance) {
        return !$instance instanceof Formatter ? Exceptions::invalidFormatException(get_class($instance)) : $instance;
    }
}