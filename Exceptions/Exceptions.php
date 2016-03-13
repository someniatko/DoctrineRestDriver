<?php
/**
 * This file is part of DoctrineRestDriverBundle.
 *
 * DoctrineRestDriverBundle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * DoctrineRestDriverBundle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with DoctrineRestDriverBundle.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Circle\DoctrineRestDriverBundle\Exceptions;

use Circle\DoctrineRestDriverBundle\Validation\Exceptions\InvalidTypeException;
use Circle\DoctrineRestDriverBundle\Validation\Exceptions\NotNilException;

/**
 * This trait contains methods to throw exceptions
 *
 * @author    Tobias Hauck <tobias.hauck@teeage-beatz.de>
 * @copyright 2015 TeeAge-Beatz UG
 *
 * @SuppressWarnings("PHPMD.StaticAccess")
 */
trait Exceptions {

    /**
     * throws an invalid type exception
     *
     * @param  string $expectedType
     * @param  string $key
     * @param  mixed  $value
     * @throws InvalidTypeException
     */
    private function invalidTypeException($expectedType, $key, $value) {
        throw new InvalidTypeException($expectedType, $key, $value);
    }

    /**
     * throws a not nil exception
     *
     * @param  string $varName
     * @throws NotNilException
     */
    private function notNilException($varName) {
        throw new NotNilException($varName);
    }
}
