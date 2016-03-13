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

namespace Circle\DoctrineRestDriverBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This is a test entity for associations
 *
 * @author    Tobias Hauck <tobias.hauck@teeage-beatz.de>
 * @copyright 2015 TeeAge-Beatz UG
 *
 * @ORM\Entity
 * @ORM\Table(name="category")
 */
class AssociatedEntity {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="Circle\DoctrineRestDriverBundle\Tests\Entity\TestEntity")
     */
    protected $product;

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return TestEntity
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param  mixed $name
     * @return TestEntity
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
}