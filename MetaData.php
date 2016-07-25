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

namespace Circle\DoctrineRestDriver;

use Circle\DoctrineRestDriver\Annotations\Loader;
use Doctrine\ORM\EntityManager;

/**
 * Provider for doctrine meta data
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 */
class MetaData {

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * MetaDataProvider constructor
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function __construct() {
        Loader::load();

        $kernel = new \AppKernel('prod', true);
        $kernel->boot();

        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
    }

    /**
     * returns all namespaces of managed entities
     *
     * @return array
     */
    public function getEntityNamespaces() {
        $meta = $this->em->getMetadataFactory()->getAllMetadata();

        return array_reduce($meta, function($carry, $item) {
            $carry[$item->table['name']] = $item->getName();
            return $carry;
        }, []);
    }
}