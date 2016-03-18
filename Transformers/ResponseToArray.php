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

namespace Circle\DoctrineRestDriver\Transformers;

use Circle\DoctrineRestDriver\Enums\SqlOperations;
use Circle\DoctrineRestDriver\Mapping\ResultMapping;
use PHPSQLParser\PHPSQLParser;
use Symfony\Component\HttpFoundation\Response;

/**
 * Response to array transformer
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 */
class ResponseToArray {

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
     * translates a given response and source query to
     * a key value store in the following format:
     * attributeName => Value
     *
     * @param  Response $response
     * @param  string   $query
     * @return array
     * @throws \Exception
     */
    public function transform(Response $response, $query) {
        $parsed   = $this->parser->parse($query);
        $operator = strtolower(array_keys($parsed)[0]);
        $content  = json_decode($response->getContent(), true);

        return $this->createResultSet($operator, $parsed, is_array($content) ? $content : []);
    }

    /**
     * returns the result set that is needed to fetch the data
     *
     * @param  string $operator
     * @param  array  $tokens
     * @param  array  $content
     * @return array
     * @throws \Exception
     */
    private function createResultSet($operator, array $tokens, array $content) {
        if ($operator === SqlOperations::DELETE) return $this->mapping->delete();
        if ($operator === SqlOperations::INSERT) return $this->mapping->insert($content);
        if ($operator === SqlOperations::UPDATE) return $this->mapping->update($content);

        return $this->mapping->select($tokens, $content);
    }
}