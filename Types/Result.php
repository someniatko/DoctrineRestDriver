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

namespace Circle\DoctrineRestDriver\Types;
use Circle\DoctrineRestDriver\Enums\SqlOperations;
use Circle\DoctrineRestDriver\MetaData;
use PHPSQLParser\PHPSQLParser;

/**
 * Maps the response content of any query to a valid
 * Doctrine result
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 */
class Result {

    /**
     * @var array
     */
    private $result;

    /**
     * @var mixed
     */
    private $id;

    /**
     * Result constructor
     *
     * @param string $query
     * @param array  $content
     */
    public function __construct($query, array $content = null) {
        $tokens       = (new PHPSQLParser())->parse($query);
        $this->result = $this->createResult($tokens, $content);
        $this->id     = $this->createId($tokens);
    }

    /**
     * Returns a valid Doctrine result
     *
     * @return array
     */
    public function get() {
        return $this->result;
    }

    /**
     * returns the id of the result
     */
    public function id() {
        return $this->id;
    }

    /**
     * returns the id
     *
     * @param  array $tokens
     * @return mixed
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    private function createId(array $tokens) {
        $idColumn = Id::column($tokens, new MetaData());
        return empty($this->result[$idColumn]) ? null : $this->result[$idColumn];
    }

    /**
     * returns the result
     *
     * @param  array      $tokens
     * @param  array|null $content
     * @return array
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    private function createResult(array $tokens, array $content = null) {
        $operator = strtolower(array_keys($tokens)[0]);

        if ($operator === SqlOperations::DELETE) return [];
        $result = $operator === SqlOperations::SELECT ? SelectResult::create($tokens, $content) : $content;
        krsort($result);

        return $result;
    }
}