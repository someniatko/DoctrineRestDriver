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

namespace Circle\DoctrineRestDriver\Events\Subscribers;

use Circle\DoctrineRestDriver\Events\BeforeRequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * RestClientOptions type
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 */
class BasicHttpAuthentication implements EventSubscriberInterface {

    /**
     * triggered when the BeforeRequest event is dispatched
     *
     * @param  BeforeRequestEvent $event
     * @return void
     */
    public function onBeforeRequest(BeforeRequestEvent $event) {
        $options  = $event->options['driverOptions'];
        $headers  = empty($options['CURLOPT_HTTPHEADER']) ? '' : $options['CURLOPT_HTTPHEADER'] . ',';
        $headers .= 'Authorization: Basic ' . base64_encode($event->options['user'] . ':' . $event->options['password']);

        $event->options['driverOptions'] = [ 'CURLOPT_HTTPHEADER' => $headers ] + $options;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() {
        return [
            BeforeRequestEvent::NAME => 'onBeforeRequest'
        ];
    }
}