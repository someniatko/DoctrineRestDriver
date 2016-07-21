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

use Circle\DoctrineRestDriver\Annotations\RoutingTable;
use Circle\DoctrineRestDriver\Enums\HttpMethods;
use Circle\DoctrineRestDriver\Enums\SqlOperations;
use Circle\DoctrineRestDriver\Exceptions\Exceptions;
use Circle\DoctrineRestDriver\Validation\Assertions;

/**
 * Table type
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 *
 * @SuppressWarnings("PHPMD.StaticAccess")
 */
class Table {

    /**
     * Returns the table name
     *
     * @param  array  $tokens
     * @return string
     */
    public static function create(array $tokens) {
        Assertions::assertHashMap('tokens', $tokens);

        if (empty($tokens['FROM']) && empty($tokens['INSERT']) && empty($tokens['UPDATE'])) return Exceptions::InvalidTypeException('array', 'tokens', null);

        $operation = SqlOperation::create($tokens);
        if ($operation === SqlOperations::INSERT) return $tokens['INSERT'][1]['no_quotes']['parts'][0];
        if ($operation === SqlOperations::UPDATE) return $tokens['UPDATE'][0]['no_quotes']['parts'][0];
        return $tokens['FROM'][0]['no_quotes']['parts'][0];
    }

    /**
     * Returns the table's alias
     *
     * @param  array  $tokens
     * @return null|string
     */
    public static function alias(array $tokens) {
        Assertions::assertHashMap('tokens', $tokens);

        $operation = SqlOperation::create($tokens);
        if ($operation === SqlOperations::INSERT) return null;
        if ($operation === SqlOperations::UPDATE) return $tokens['UPDATE'][0]['alias']['name'];
        return $tokens['FROM'][0]['alias']['name'];
    }

    /**
     * replaces the table in the tokens array with the given table
     *
     * @param  array $tokens
     * @param  array $newTable
     * @return array
     */
    public static function replace(array $tokens, $newTable) {
        Assertions::assertHashMap('tokens', $tokens);

        $operation = SqlOperation::create($tokens);
        if ($operation === SqlOperations::INSERT) $tokens['INSERT'][1]['no_quotes']['parts'][0] = $newTable;
        if ($operation === SqlOperations::UPDATE) $tokens['UPDATE'][0]['no_quotes']['parts'][0] = $newTable;
        $tokens['FROM'][0]['no_quotes']['parts'][0] = $newTable;

        return $tokens;
    }

    /**
     * replaces the table name with the related annotation
     *
     * @param  array        $tokens
     * @param  RoutingTable $annotations
     * @return array
     */
    public static function replaceWithAnnotation(array $tokens, RoutingTable $annotations = null) {
        if (empty($annotations)) return $tokens;

        $table        = Table::create($tokens);
        $method       = HttpMethods::ofSqlOperation(SqlOperation::create($tokens));
        $id           = Id::create($tokens);
        $methodToCall = $method === HttpMethods::GET && empty($id) ? $method . 'All' : $method;

        return !empty($annotations) && $annotations->get($table) !== null && $annotations->get($table)->$methodToCall() !== null ? Table::replace($tokens, $annotations->get($table)->$methodToCall()) : $tokens;
    }
}