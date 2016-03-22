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

namespace Circle\DoctrineRestDriver\Factory;

use Circle\DoctrineRestDriver\Enums\SqlOperations;
use Circle\DoctrineRestDriver\Mapping\ResultMapping;
use PHPSQLParser\PHPSQLParser;
use Symfony\Component\HttpFoundation\Response;

/**
 * Factory for result sets
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 */
class ResultSetFactory {

    /**
     * @var PHPSQLParser
     */
    private $parser;

    /**
     * @var ResultMapping
     */
    private $mapping;

    /**
     * ResponseToArray constructor
     */
    public function __construct() {
        $this->parser  = new PHPSQLParser();
        $this->mapping = new ResultMapping();
    }

    /**
     * translates a given response and sql query to
     * a key value store in the following format:
     * attributeName => value
     *
     * @param  Response $response
     * @param  string   $query
     * @return array
     */
    public function createOne(Response $response, $query) {
        $tokens   = $this->parser->parse($query);
        $operator = strtolower(array_keys($tokens)[0]);
        $content  = json_decode($response->getContent(), true);

        if ($operator === SqlOperations::DELETE) return $this->mapping->delete();
        if ($operator === SqlOperations::INSERT) return $this->mapping->insert($content);
        if ($operator === SqlOperations::UPDATE) return $this->mapping->update($content);

        return $this->mapping->select($tokens, $content);
    }
}