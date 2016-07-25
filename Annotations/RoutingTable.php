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
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * Contains all routing information about all entities
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 */
class RoutingTable {

    /**
     * @var array
     */
    private $routingTable = [];

    /**
     * @var AnnotationReader
     */
    private $reader;

    /**
     * @var array
     */
    private $annotations = [
        'post'   => 'Circle\DoctrineRestDriver\Annotations\Insert',
        'put'    => 'Circle\DoctrineRestDriver\Annotations\Update',
        'get'    => 'Circle\DoctrineRestDriver\Annotations\Select',
        'delete' => 'Circle\DoctrineRestDriver\Annotations\Delete',
        'getAll' => 'Circle\DoctrineRestDriver\Annotations\Fetch'
    ];

    /**
     * RoutingTable constructor
     *
     * @param array $entities
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function __construct(array $entities) {
        Assertions::assertHashMap('entities', $entities);

        $this->reader = new AnnotationReader();

        $aliases = array_flip($entities);
        $this->routingTable = array_reduce($entities, function ($carry, $namespace) use ($aliases) {
            $refl  = new \ReflectionClass($namespace);

            $annotations = array_flip($this->annotations);
            $routes      = array_reduce($this->annotations, function($carry, $annotation) use ($refl, $annotations) {
                $carry[$annotations[$annotation]] = $this->reader->getClassAnnotation($refl, $annotation);

                return $carry;
            }, []);

            $carry[$aliases[$namespace]] = new Routing($routes);

            return $carry;
        }, []);
    }

    /**
     * returns the routing information about the entity alias
     *
     * @param  string $alias
     * @return Routing
     */
    public function get($alias) {
        return !empty($this->routingTable[$alias]) ? $this->routingTable[$alias] : null;
    }
}