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

use Circle\DoctrineRestDriver\Enums\SqlOperations;
use Circle\DoctrineRestDriver\Validation\Assertions;

/**
 * Table type
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 */
class Table {

    /**
     * Returns the table name
     *
     * @param  array  $tokens
     * @return string
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public static function create(array $tokens) {
        Assertions::assertHashMap('tokens', $tokens);

        $expression = self::expression($tokens);
        $parts      = explode(' ', $expression);
        return $parts[0];
    }

    /**
     * Returns the table's alias
     *
     * @param  array  $tokens
     * @return null|string
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public static function alias(array $tokens) {
        Assertions::assertHashMap('tokens', $tokens);

        $operation = SqlOperation::create($tokens);
        if ($operation === SqlOperations::INSERT) return null;

        $expression = self::expression($tokens);
        $parts      = explode(' ', $expression);
        return count($parts) > 1 ? end($parts) : null;
    }

    /**
     * Returns the table expression: table name and alias
     * if exists
     *
     * @param  array $tokens
     * @return string
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public static function expression(array $tokens) {
        Assertions::assertHashMap('tokens', $tokens);

        $operation = SqlOperation::create($tokens);
        if ($operation === SqlOperations::INSERT) return str_replace('"', '', $tokens['INSERT'][1]['base_expr']);
        if ($operation === SqlOperations::UPDATE) return str_replace('"', '', $tokens['UPDATE'][0]['base_expr']);
        return str_replace('"', '', $tokens['FROM'][0]['base_expr']);
    }
}