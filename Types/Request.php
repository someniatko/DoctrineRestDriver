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

namespace Circle\DoctrineRestDriverBundle\Types;

/**
 * Request type
 *
 * @author    Tobias Hauck <tobias.hauck@teeage-beatz.de>
 * @copyright 2015 TeeAge-Beatz UG
 */
class Request {

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $payload;

    /**
     * Request constructor
     *
     * @param string      $method
     * @param string      $url
     * @param string|null $query
     * @param string|null $payload
     */
    public function __construct($method, $url, $query = null, $payload = null) {
        $this->method  = $method;
        $this->url     = empty($query) ? $url : $url . '?' . $query;
        $this->payload = $payload;
    }

    /**
     * returns the method
     *
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * returns the url
     *
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * returns the payload
     *
     * @return string
     */
    public function getPayload() {
        return $this->payload;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString() {
        return $this->method . ' ' . $this->url . ' HTTP/1.1' . (!empty($this->payload) ? ' ' . $this->payload : '');
    }
}