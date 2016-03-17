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

use Circle\DoctrineRestDriver\Factory\RestClientFactory;
use Circle\DoctrineRestDriver\Validation\Assertions;

/**
 * Options type for oauth authentication
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 */
abstract class OAuthOptions extends \ArrayObject {
    use Assertions;

    /**
     * Security constructor
     *
     * @param string $username
     * @param string $password
     * @param array  $options
     */
    public function __construct($username, $password, array $options) {
        $this->validate($username, $password, $options);
        parent::__construct($this->format($username, $password, $options));
    }

    /**
     * creates the request to get the auth token
     *
     * @param  string $username
     * @param  string $password
     * @param  array $options
     * @return Request
     */
    protected abstract function createTokenRequest($username, $password, array $options);

    /**
     * returns the header string
     *
     * @param  mixed $content
     * @return string
     */
    protected abstract function createOAuthHeaderString($content);

    /**
     * returns the formatted options
     *
     * @param  string $username
     * @param  string $password
     * @param  array $options
     * @return array
     */
    private function format($username, $password, array $options) {
        $headers = $options[CURLOPT_HTTPHEADER];
        array_push($headers, $this->getOAuthHeaderString($username, $password, $options));
        $headers = [ CURLOPT_HTTPHEADER => $headers ];

        return $headers + $options;
    }

    /**
     * creates the oauth header string including the security token
     *
     * @param  string $username
     * @param  string $password
     * @param  array $options
     * @return string
     */
    private function getOAuthHeaderString($username, $password, array $options) {
        $restClientFactory = new RestClientFactory();
        $restClient        = $restClientFactory->createOne(new RestClientOptions([
            'user'     => $username,
            'password' => $password,
            'driverOptions' => [
                'security_strategy' => 'basic_http'
            ]
        ]));

        $request  = $this->createTokenRequest($username, $password, $options);
        $method   = strtolower($request->getMethod());
        $response = $method === 'get' || $method === 'delete' ? $restClient->$method($request->getUrl()) : $restClient->$method($request->getUrl(), $request->getPayload());

        return $this->createOAuthHeaderString($response->getContent());
    }

    /**
     * validates the given input
     *
     * @param  string      $username
     * @param  string|null $password
     * @param  array       $options
     * @return void
     */
    private function validate($username, $password, $options) {
        $this->assertList('options',                     $options);
        $this->assertString('username',                  $username);
        $this->assertMaybeString('password',             $password);
        $this->assertListEntryExists('options',          $options, CURLOPT_HTTPHEADER);
        $this->assertList('options[CURLOPT_HTTPHEADER]', $options[CURLOPT_HTTPHEADER]);
    }
}