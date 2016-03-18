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

namespace Circle\DoctrineRestDriver\Events;
use Circle\DoctrineRestDriver\Types\Request;
use Symfony\Component\EventDispatcher\Event;

/**
 * This event is emitted before a request is sent
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 */
class BeforeRequestEvent extends Event {
    const NAME = 'before.request';

    /**
     * @var Request
     */
    public $request;

    /**
     * @var array
     */
    public $options;

    /**
     * BeforeRequestEvent constructor
     *
     * @param Request $request
     * @param array   $options
     */
    public function __construct(Request $request, array $options) {
        $this->request = $request;
        $this->options = $options;
    }
}