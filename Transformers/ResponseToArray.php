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

namespace Circle\DoctrineRestDriverBundle\Transformers;

use PHPSQLParser\PHPSQLParser;
use Symfony\Component\HttpFoundation\Response;

/**
 * Response to array transformer
 *
 * @author    Tobias Hauck <tobias.hauck@teeage-beatz.de>
 * @copyright 2015 TeeAge-Beatz UG
 */
class ResponseToArray {

    /**
     * @var PHPSQLParser
     */
    private $parser;

    /**
     * ResponseToArray constructor
     */
    public function __construct() {
        $this->parser = new PHPSQLParser();
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
    public function trans(Response $response, $query) {
        $parsed   = $this->parser->parse($query);
        $operator = strtolower(array_keys($parsed)[0]);

        return $this->createResultSet($operator, $parsed, $response);
    }

    /**
     * returns the result set that is needed to fetch the data
     *
     * @param  string $operator
     * @param  array $tokens
     * @param  Response $response
     * @return array
     * @throws \Exception
     */
    private function createResultSet($operator, array $tokens, Response $response) {
        if ($operator === 'delete')                           return [];
        if ($operator === 'insert' || $operator === 'update') return $this->getContent($response);

        return $this->select($tokens, $response);
    }

    /**
     * returns the select result set
     *
     * @param  array $tokens
     * @param  Response $response
     * @return array
     * @throws \Exception
     */
    private function select(array $tokens, Response $response) {
        $content = $this->getContent($response);
        if (empty($content)) throw new \Exception('ResponseToArray.select: unhandled');
        return empty($content[0]) ? $this->selectSingle($tokens, $content) : $this->selectAll($tokens, $content);
    }

    /**
     * returns the select single result set
     *
     * @param  array $tokens
     * @param  array $content
     * @return array
     * @throws \Exception
     */
    private function selectSingle(array $tokens, array $content) {
        $tableAlias = $this->getTableAlias($tokens);

        $attributeValueMap = array_map(function($token) use ($content, $tableAlias) {
            $key   = empty($token['alias']['name']) ? $token['base_expr'] : $token['alias']['name'];
            $value = $content[str_replace($tableAlias . '.', '', $token['base_expr'])];
            return [$key => $value];
        }, $tokens['SELECT']);

        return [ array_reduce($attributeValueMap, 'array_merge', []) ];
    }

    /**
     * returns the select all result set
     *
     * @param  array $tokens
     * @param  array $content
     * @return array
     */
    private function selectAll(array $tokens, array $content) {
        $content = $this->orderBy($tokens, $content);

        return array_map(function($entry) use ($tokens) {
            $row = $this->selectSingle($tokens, $entry);
            return array_pop($row);
        }, $content);
    }

    /**
     * returns the table alias
     *
     * @param  array $tokens
     * @return mixed
     * @throws \Exception
     */
    private function getTableAlias(array $tokens) {
        return $tokens['FROM'][0]['alias']['name'];
    }

    /**
     * returns the content
     *
     * @param  Response $response
     * @return array
     */
    private function getContent(Response $response) {
        return json_decode($response->getContent(), true);
    }

    /**
     * orders the content with the given order by criteria
     *
     * @param  array $tokens
     * @param  array $content
     * @return array
     */
    private function orderBy(array $tokens, array $content) {
        if (empty($tokens['ORDER'])) return $content;

        $sortingRules = array_map(function($token) use ($content) {
            return [
                end($token['no_quotes']['parts']),
                $token['direction']
            ];
        }, $tokens['ORDER']);

        $reducedSortingRules = array_reduce($sortingRules, 'array_merge', []);
        $sortArgs            = array_map(function($value) use ($content) {
            if ($value === 'ASC')  return SORT_ASC;
            if ($value === 'DESC') return SORT_DESC;

            $contents = [];
            foreach ($content as $c) array_push($contents, $c[$value]);
            return $contents;
        }, $reducedSortingRules);

        $sortArgs[] = &$content;
        call_user_func_array('array_multisort', $sortArgs);

        return end($sortArgs);
    }
}