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

use Circle\DoctrineRestDriver\Enums\HttpMethods;
use Circle\DoctrineRestDriver\Enums\SqlOperations;
use Circle\DoctrineRestDriver\Types\Request;
use Circle\DoctrineRestDriver\Validation\Assertions;
use PHPSQLParser\PHPSQLParser;

/**
 * Mysql to Request transformer
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 */
class MysqlToRequest {
    use Assertions;

    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @var PHPSQLParser
     */
    private $parser;

    /**
     * MysqlToRequest constructor
     *
     * @param string $apiUrl
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function __construct($apiUrl) {
        $this->apiUrl = $apiUrl;
        $this->parser = new PHPSQLParser();
    }

    /**
     * translates the given query into a request object
     *
     * @param  string $query
     * @param  array  $params
     * @return Request
     */
    public function trans($query, array $params = []) {
        $query    = $this->setParams($query, $params);
        $parsed   = $this->parser->parse($query);
        $operator = strtolower(array_keys($parsed)[0]);

        return $this->createRequest($parsed, $operator);
    }

    /**
     * creates the request
     *
     * @param  array  $tokens
     * @param  string $operator
     * @return Request
     * @throws \Exception
     */
    private function createRequest(array $tokens, $operator) {
        $method  = $this->getMethod($operator);
        $url     = $this->getUrl($tokens, $operator);
        $query   = $this->getQuery($tokens, $operator);
        $payload = $this->getPayload($tokens, $operator);

        return new Request($method, $url, $query, $payload);
    }

    /**
     * @param  string $operator
     * @return string
     * @throws \Exception
     */
    private function getMethod($operator) {
        if ($operator === SqlOperations::INSERT) return HttpMethods::POST;
        if ($operator === SqlOperations::SELECT) return HttpMethods::GET;
        if ($operator === SqlOperations::UPDATE) return HttpMethods::PUT;
        if ($operator === SqlOperations::DELETE) return HttpMethods::DELETE;

        throw new \Exception('Invalid operator ' . $operator . ' in sql query');
    }

    /**
     * returns the url
     *
     * @param  array  $tokens
     * @param  string $operator
     * @return string
     */
    private function getUrl(array $tokens, $operator) {
        $table = $this->getTable($tokens, $operator);
        $id    = empty($this->getId($tokens)) ? '' : '/' . $this->getId($tokens);

        return $this->isUrl($table) ? $table . $id : $this->apiUrl . '/' . $table . $id;
    }

    /**
     * returns the payload
     *
     * @param  array       $tokens
     * @param  string      $operator
     * @return null|string
     */
    private function getPayload(array $tokens, $operator) {
        if ($operator === SqlOperations::SELECT || $operator === SqlOperations::DELETE) return null;
        return $operator === SqlOperations::INSERT ? $this->getInsertPayload($tokens) : $this->getUpdatePayload($tokens);
    }

    /**
     * returns the table name
     *
     * @param  array  $tokens
     * @param  string $operator
     * @return string
     */
    private function getTable(array $tokens, $operator) {
        if ($operator === SqlOperations::UPDATE) return str_replace('\'', '', $tokens['UPDATE'][0]['table']);
        if ($operator === SqlOperations::INSERT) return str_replace('\'', '', $tokens['INSERT'][1]['table']);
        return str_replace('\'', '', $tokens['FROM'][0]['table']);
    }

    /**
     * returns the query
     *
     * @param  array       $tokens
     * @param  string      $operator
     * @return null|string
     * @throws \Exception
     */
    private function getQuery(array $tokens, $operator) {
        if ($operator !== SqlOperations::SELECT || empty($tokens['WHERE'])) return null;

        $tableAlias = $this->getTableAlias($tokens);
        $query      = array_reduce($tokens['WHERE'], function($query, $token) use ($tableAlias) {
            return $query . str_replace('"', '', str_replace('OR', '|', str_replace('AND', '&', str_replace($tableAlias . '.', '', $token['base_expr']))));
        });

        return preg_replace('/id\=\d*&*/', '', $query);
    }

    /**
     * returns the id
     *
     * @param  array       $tokens
     * @return null|string
     * @throws \Exception
     */
    private function getId(array $tokens) {
        if (empty($tokens['WHERE'])) return null;

        $idAlias = $this->getIdAlias($tokens);

        return array_reduce($tokens['WHERE'], function($carry, $token) use ($tokens, $idAlias) {
            if (!is_int($carry)) return $carry;
            if ($token['expr_type'] === 'colref' && $token['base_expr'] === $idAlias) return $tokens['WHERE'][$carry+2]['base_expr'];
        }, 0);
    }

    /**
     * returns the alias for the id attribute
     *
     * @param  array $tokens
     * @return string
     * @throws \Exception
     */
    private function getIdAlias(array $tokens) {
        $tableAlias = $this->getTableAlias($tokens);
        return empty($tableAlias) ? 'id' : $this->getTableAlias($tokens) . '.id';
    }

    /**
     * returns the payload if the given query is an insert one
     *
     * @param  array  $tokens
     * @return string
     */
    private function getInsertPayload(array $tokens) {
        $columns = array_filter($tokens['INSERT'], function($token) {
            return $token['expr_type'] === 'column-list';
        });

        $columns = explode(',', str_replace(['(', ')', ' '], '', end($columns)['base_expr']));
        $values  = explode(',', str_replace(['(', ')', ' '], '', end($tokens['VALUES'])['base_expr']));

        return json_encode(array_combine($columns, $values));
    }

    /**
     * returns the payload if the given query is an update one
     *
     * @param  array  $tokens
     * @return string
     */
    private function getUpdatePayload(array $tokens) {
        $columns = array_map(function($token) {
            $segments = explode('=', $token['base_expr']);
            return $segments[0];
        }, $tokens['SET']);

        $values = array_map(function($token) {
            $segments = explode('=', $token['base_expr']);
            return str_replace('"', '', $segments[1]);
        }, $tokens['SET']);

        return json_encode(array_combine($columns, $values));
    }

    /**
     * returns the alias of the table
     *
     * @param  array      $tokens
     * @return string
     * @throws \Exception
     */
    private function getTableAlias(array $tokens) {
        if (!empty($tokens['INSERT'])) return $tokens['INSERT'][1]['alias']['name'];
        if (!empty($tokens['UPDATE'])) return $tokens['UPDATE'][0]['alias']['name'];
        return $tokens['FROM'][0]['alias']['name'];
    }

    /**
     * replaces all variable placeholder with its values
     *
     * @param  string $query
     * @param  array  $params
     * @return string
     *
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    private function setParams($query, array $params) {
        return array_reduce($params, function($newQuery, $param) {
            return $this->setParam($newQuery, $param);
        }, $query);
    }

    /**
     * sets a param to the query
     *
     * @param  string $query
     * @param  mixed  $param
     * @return string
     */
    private function setParam($query, $param) {
        $pos = strpos($query, '?');
        return $pos ? substr_replace($query, $param, $pos, strlen('?')) : $query;
    }
}