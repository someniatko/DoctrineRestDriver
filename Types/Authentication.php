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

use Circle\DoctrineRestDriver\Security\AuthStrategy;
use Circle\DoctrineRestDriver\Validation\Assertions;

/**
 * Type for Authentication
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 */
class Authentication {

    /**
     * Returns the right HTTP method
     *
     * @param  array  $options
     * @return AuthStrategy
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public static function create(array $options) {
        $authenticatorClass = !empty($options['driverOptions']['authenticator_class']) ? $options['driverOptions']['authenticator_class'] : 'NoAuthentication';
        $className          = preg_match('/\\\\/', $authenticatorClass) ? $authenticatorClass : 'Circle\DoctrineRestDriver\Security\\' . $authenticatorClass;
        Assertions::assertClassExists($className);

        return Assertions::assertAuthStrategy(new $className($options));
    }
}