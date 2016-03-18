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

use Circle\DoctrineRestDriver\Enums\HttpMethods;
use Circle\DoctrineRestDriver\Events\BeforeRequestEvent;
use Circle\DoctrineRestDriver\Factory\RestClientFactory;
use Circle\DoctrineRestDriver\Transformers\MysqlToRequest;
use Circle\DoctrineRestDriver\Transformers\ResponseToArray;
use Circle\DoctrineRestDriver\Types\Request;
use Circle\DoctrineRestDriver\Types\RestClientOptions;
use Circle\DoctrineRestDriver\Validation\Assertions;
use Doctrine\DBAL\Driver\Statement as StatementInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Response;

/**
 * Executes the statement - sends requests to an api
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 *
 * @SuppressWarnings("PHPMD.TooManyPublicMethods")
 */
class Statement implements \IteratorAggregate, StatementInterface {
    use Assertions;

    /**
     * @var string
     */
    private $query;

    /**
     * @var MysqlToRequest
     */
    private $mysqlToRequest;

    /**
     * @var array
     */
    private $params = [];

    /**
     * @var \Circle\DoctrineRestDriver\Factory\RestClientFactory
     */
    private $restClientFactory;

    /**
     * @var array
     */
    private $resultSet;

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $errorCode;

    /**
     * @var string
     */
    private $errorMessage;

    /**
     * @var int
     */
    private $fetchMode;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @var RestClientOptions
     */
    private $options;

    /**
     * Statement constructor
     *
     * @param string $query
     * @param array  $options
     */
    public function __construct($query, array $options) {
        $this->query             = $query;
        $this->mysqlToRequest    = new MysqlToRequest($options['host']);
        $this->restClientFactory = new RestClientFactory();
        $this->options           = $options;
        $this->dispatcher        = new EventDispatcher();

        $authenticatorClass = !empty($options['driverOptions']['authenticator_class']) ? $options['driverOptions']['authenticator_class'] : null;
        $this->assertClassExists($authenticatorClass);
        $this->dispatcher->addSubscriber(new $authenticatorClass);
    }

    /**
     * {@inheritdoc}
     */
    public function bindValue($param, $value, $type = null) {
        $this->params[$param] = $value;
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function bindParam($column, &$variable, $type = null, $length = null) {
        throw new \Exception('not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function errorCode() {
        return $this->errorCode;
    }

    /**
     * {@inheritdoc}
     */
    public function errorInfo() {
        return $this->errorMessage;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($params = null) {
        $request    = $this->mysqlToRequest->transform($this->query, $this->params);
        $event      = $this->dispatcher->dispatch(BeforeRequestEvent::NAME, new BeforeRequestEvent($request, (array) $this->options));
        $restClient = $this->restClientFactory->createOne(new RestClientOptions($event->options));

        $method     = strtolower($event->request->getMethod());
        $response   = $method === HttpMethods::GET || $method === HttpMethods::DELETE ? $restClient->$method($event->request->getUrl()) : $restClient->$method($event->request->getUrl(), $event->request->getPayload());
        $statusCode = $response->getStatusCode();

        return $statusCode === 200 || $method === HttpMethods::DELETE && $statusCode === 204 ? $this->onSuccess($response, $method) : $this->onError($event->request, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function rowCount() {
        return count($this->resultSet);
    }

    /**
     * {@inheritdoc}
     */
    public function closeCursor() {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function columnCount() {
        return empty($this->resultSet) ? 0 : count($this->resultSet[0]);
    }

    /**
     * {@inheritdoc}
     */
    public function setFetchMode($fetchMode, $arg2 = null, $arg3 = null) {
        $this->fetchMode = $fetchMode;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($fetchMode = null) {
        $fetchMode = empty($fetchMode) ? $this->fetchMode : $fetchMode;
        $this->assertSupportedFetchMode($fetchMode);

        return count($this->resultSet) === 0 ? false : array_pop($this->resultSet);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAll($fetchMode = null) {
        $result    = [];
        $fetchMode = empty($fetchMode) ? $this->fetchMode : $fetchMode;

        while (($row = $this->fetch($fetchMode))) array_push($result, $row);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchColumn($columnIndex = 0) {
        throw new \Exception('not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator() {
        return $this->query;
    }

    /**
     * returns the last auto incremented id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * handles the statement if the execution succeeded
     *
     * @param  Response $response
     * @param  string   $method
     * @return bool
     */
    private function onSuccess(Response $response, $method) {
        $responseToArray = new ResponseToArray();
        $this->resultSet = $responseToArray->transform($response, $this->query);
        $this->id        = $method === HttpMethods::POST ? $this->resultSet['id'] : null;
        krsort($this->resultSet);

        return true;
    }

    /**
     * handles the statement if the execution failed
     *
     * @param  Request  $request
     * @param  Response $response
     * @throws \Exception
     */
    private function onError(Request $request, Response $response) {
        $this->errorCode    = $response->getStatusCode();
        $this->errorMessage = $response->getContent();

        throw new \Exception('Execution failed for request: ' . $request . ': HTTPCode ' . $this->errorCode() . ', body ' . $this->errorMessage);
    }
}