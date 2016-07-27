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

namespace Circle\DoctrineRestDriver\Annotations;

/**
 * Contains routing information about a specific entity
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 */
class Routing {

    /**
     * @var Insert
     */
    private $post;

    /**
     * @var Update
     */
    private $put;

    /**
     * @var Select
     */
    private $get;

    /**
     * @var Delete
     */
    private $delete;

    /**
     * @var Fetch
     */
    private $getAll;

    /**
     * @var array
     */
    private static $annotations = [
        'post'   => 'Circle\DoctrineRestDriver\Annotations\Insert',
        'put'    => 'Circle\DoctrineRestDriver\Annotations\Update',
        'get'    => 'Circle\DoctrineRestDriver\Annotations\Select',
        'delete' => 'Circle\DoctrineRestDriver\Annotations\Delete',
        'getAll' => 'Circle\DoctrineRestDriver\Annotations\Fetch'
    ];

    /**
     * Routing constructor
     *
     * @param string $namespace
     */
    public function __construct($namespace) {
        $reader = new Reader();
        $class  = new \ReflectionClass($namespace);

        foreach (self::$annotations as $alias => $annotation) $this->$alias = $reader->read($class, $annotation);
    }

    /**
     * returns the post route
     *
     * @return string|null
     */
    public function post() {
        return $this->post;
    }

    /**
     * returns the get route
     *
     * @return string|null
     */
    public function get() {
        return $this->get;
    }

    /**
     * returns the put route
     *
     * @return string|null
     */
    public function put() {
        return $this->put;
    }

    /**
     * returns the delete route
     *
     * @return string|null
     */
    public function delete() {
        return $this->delete;
    }

    /**
     * returns the get all route
     *
     * @return string|null
     */
    public function getAll() {
        return $this->getAll;
    }
}