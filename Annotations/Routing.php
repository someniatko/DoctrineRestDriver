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
use Circle\DoctrineRestDriver\Validation\Assertions;
use Circle\DoctrineRestDriver\Exceptions\Exceptions;

/**
 * Contains routing information about a specific entity
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 */
class Routing {

    /**
     * @var Post
     */
    private $post;

    /**
     * @var Put
     */
    private $put;

    /**
     * @var Get
     */
    private $get;

    /**
     * @var Delete
     */
    private $delete;

    /**
     * @var GetAll
     */
    private $getAll;

    /**
     * Routing constructor
     *
     * @param Post   $post
     * @param Put    $put
     * @param Get    $get
     * @param Delete $delete
     */
    public function __construct(Post $post = null, Put $put = null, Get $get = null, Delete $delete = null, GetAll $getAll = null) {
        $this->post   = $post;
        $this->put    = $put;
        $this->get    = $get;
        $this->delete = $delete;
        $this->getAll = $getAll;
    }

    /**
     * returns the post route
     *
     * @return string|null
     */
    public function post() {
        return empty($this->post) ? null : $this->post->getRoute();
    }

    /**
     * returns the get route
     *
     * @return string|null
     */
    public function get() {
        return empty($this->get) ? null : $this->get->getRoute();
    }

    /**
     * returns the put route
     *
     * @return string|null
     */
    public function put() {
        return empty($this->put) ? null : $this->put->getRoute();
    }

    /**
     * returns the delete route
     *
     * @return string|null
     */
    public function delete() {
        return empty($this->delete) ? null : $this->delete->getRoute();
    }

    /**
     * returns the get all route
     *
     * @return string|null
     */
    public function getAll() {
        return empty($this->getAll) ? null : $this->getAll->getRoute();
    }
}