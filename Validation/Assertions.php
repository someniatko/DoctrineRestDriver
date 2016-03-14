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

use Circle\DoctrineRestDriver\Validation\Exceptions\InvalidTypeException;
use Circle\DoctrineRestDriver\Validation\Exceptions\NotNilException;
use Circle\DoctrineRestDriver\Exceptions\Exceptions;

/**
 * Trait including assertions
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 */
trait Assertions {
    use Exceptions;

    /**
     * asserts if the given value is not nil
     *
     * @param  string     $varName
     * @param  mixed      $value
     * @return Assertions
     * @throws NotNilException
     */
    private function assertNotNil($varName, $value) {
        if($value === null) return $this->notNilException($varName);

        return $this;
    }

    /**
     * asserts if the given value is a string
     *
     * @param  string     $varName
     * @param  mixed      $value
     * @return Assertions
     * @throws InvalidTypeException
     * @throws NotNilException
     */
    private function assertString($varName, $value) {
        $this->assertNotNil($varName, $value);
        if(!is_string($value)) return $this->invalidTypeException('string', $varName, $value);

        return $this;
    }

    /**
     * asserts if the given value is a string or null
     *
     * @param  string     $varName
     * @param  mixed      $value
     * @return Assertions
     * @throws InvalidTypeException
     */
    private function assertMaybeString($varName, $value) {
        if(!is_string($value) && $value !== null) return $this->invalidTypeException('string', $varName, $value);

        return $this;
    }

    /**
     * asserts if the given value is a list
     *
     * @param  string     $varName
     * @param  mixed      $value
     * @return Assertions
     * @throws InvalidTypeException
     * @throws NotNilException
     */
    private function assertList($varName, $value) {
        $this->assertNotNil($varName, $value);
        if(!is_array($value)) return $this->invalidTypeException('array', $varName, $value);

        $nonNumericalKeys = array_filter(array_keys($value), function($key) {
            return !is_numeric($key);
        });
        if(count($nonNumericalKeys) > 0) return $this->invalidTypeException('int', $varName, $nonNumericalKeys[0]);

        return $this;
    }

    /**
     * asserts if the given value is a list
     *
     * @param  string     $varName
     * @param  array      $list
     * @param  int        $key
     * @return Assertions
     * @throws InvalidTypeException
     * @throws NotNilException
     */
    private function assertListEntryExists($varName, $list, $key) {
        $this->assertList($varName, $list);
        if (!is_int($key)) return $this->invalidTypeException('int', $varName . '[-->KEY<--]', $key);
        if (!array_key_exists($key, $list)) return $this->invalidTypeException('ListEntry', $varName . '[\'' . $key . '\']', 'undefined');

        return $this;
    }

    /**
     * asserts if the given value is a hash map
     *
     * @param  string     $varName
     * @param  mixed      $value
     * @return Assertions
     * @throws InvalidTypeException
     */
    private function assertHashMap($varName, $value) {
        if(!is_array($value)) return $this->invalidTypeException('HashMap', $varName, $value);
        foreach($value as $key => $v) $this->assertHashMapEntry($varName, [$key => $v]);

        return $this;
    }

    /**
     * asserts if the given value is a hash map entry
     *
     * @param  string      $varName
     * @param  mixed       $value
     * @return Assertions
     * @throws InvalidTypeException
     */
    private function assertHashMapEntry($varName, $value) {
        if(!is_array($value)) return $this->invalidTypeException('HashMapEntry', $varName, $value);
        if(count($value) > 1) return $this->invalidTypeException('HashMapEntry', $varName, $value);

        $keys   = array_keys($value);
        $key    = end($keys);
        $this->assertString('HashMapEntry of "' . $varName . '": "' . $key . '"', $key);

        return $this;
    }

    /**
     * asserts if the given value is a hash map entry
     *
     * @param  string      $varName
     * @param  array       $hashMap
     * @param  string      $entryName
     * @return Assertions
     * @throws InvalidTypeException
     */
    private function assertHashMapEntryExists($varName, $hashMap, $entryName) {
        $this->assertHashMap($varName, $hashMap);
        return array_key_exists($entryName, $hashMap) ? $this : $this->invalidTypeException('HashMapEntry', $varName . '[\'' . $entryName . '\']', 'undefined');
    }

    /**
     * checkes if the given value is a url
     *
     * @param  mixed $value
     * @return bool
     */
    private function isUrl($value) {
        return (preg_match('/^(http|ftp|https):\/\/[0-9a-zA-Z_-]+(\.[0-9a-zA-Z_-]+)+([0-9a-zA-Z_\-.,@?^=%&amp;:\/~+#-]*[0-9a-zA-Z_\-@?^=%&amp;\/~+#-])?/', $value));
    }
}