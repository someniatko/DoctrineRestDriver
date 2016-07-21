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

use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * Loader for annotations
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 */
class Loader {

    /**
     * loads all annotations of the driver
     *
     * @return void
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public static function load() {
        AnnotationRegistry::registerFile(__DIR__ . DIRECTORY_SEPARATOR . 'Post.php');
        AnnotationRegistry::registerFile(__DIR__ . DIRECTORY_SEPARATOR . 'Put.php');
        AnnotationRegistry::registerFile(__DIR__ . DIRECTORY_SEPARATOR . 'Get.php');
        AnnotationRegistry::registerFile(__DIR__ . DIRECTORY_SEPARATOR . 'Delete.php');
    }
}