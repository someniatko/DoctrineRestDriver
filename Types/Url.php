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

use Circle\DoctrineRestDriver\Annotations\DataSource;
use Circle\DoctrineRestDriver\Validation\Assertions;

/**
 * Url type
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 */
class Url {

    /**
     * returns an url depending on the given route, apiUrl
     * and id
     *
     * @param  string      $route
     * @param  string      $apiUrl
     * @param  string|null $id
     * @return string
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public static function create($route, $apiUrl, $id = null) {
        Assertions::assertString('route', $route);
        Assertions::assertString('apiUrl', $apiUrl);
        Assertions::assertMaybeString('id', $id);

        $idPath = empty($id) ? '' : '/' . $id;

        if (!Assertions::isUrl($route))      return $apiUrl . '/' . $route . $idPath;
        if (!preg_match('/\{id\}/', $route)) return $route . $idPath;

        return !empty($id) ? str_replace('{id}', $id, $route) : str_replace('/{id}', '', $route);
    }

    /**
     * returns an url depending on the given tokens
     *
     * @param  array      $tokens
     * @param  string     $apiUrl
     * @param  DataSource $annotation
     * @return string
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public static function createFromTokens(array $tokens, $apiUrl, DataSource $annotation = null) {
        $id    = Id::create($tokens);
        $route = empty($annotation) || $annotation->getRoute() === null ? Table::create($tokens) : $annotation->getRoute();

        return self::create($route, $apiUrl, $id);
    }
}