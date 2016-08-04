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

use Circle\DoctrineRestDriver\Exceptions\Exceptions;
use Circle\DoctrineRestDriver\Validation\Assertions;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait for all annotations regarding routes
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 *
 * @Annotation
 */
trait Route {

    /**
     * @var string
     */
    private $route;

    /**
     * @var string
     */
    private $method;

    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var array
     */
    private $options = [];

    /**
     * Constructor
     *
     * @param array $values
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function __construct(array $values) {
        $settings = new ArrayCollection($values);

        $this->route      = Assertions::assertUrl('value', $settings->get('value'));
        $this->statusCode = Assertions::assertMaybeInt('statusCode', $settings->get('statusCode'));
        $this->method     = Assertions::assertMaybeString('method', $settings->get('method'));
        $this->options    = Assertions::assertMaybeList('options', $settings->get('options'));
    }

    /**
     * returns the route
     *
     * @return string
     */
    public function getRoute() {
        return $this->route;
    }

    /**
     * returns the status code
     *
     * @return int|null
     */
    public function getStatusCode() {
        return $this->statusCode;
    }

    /**
     * returns the method
     *
     * @return string|null
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * returns the options
     *
     * @return array|null
     */
    public function getOptions() {
        return $this->options;
    }
}