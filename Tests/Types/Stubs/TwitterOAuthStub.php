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

namespace Circle\DoctrineRestDriver\Tests\Types\Stubs;

use Circle\DoctrineRestDriver\Types\OAuthOptions;
use Circle\DoctrineRestDriver\Types\Request;

/**
 * Stub to simulate using twitters oauth authentication
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 */
class TwitterOAuthStub extends OAuthOptions {

    /**
     * {@inheritdoc}
     */
    protected function createTokenRequest($username, $password, array $options) {
        return new Request('POST', 'http://localhost:3000/app_dev.php/mockapi/oauth2/token', null, 'grant_type=client_credentials');
    }

    /**
     * {@inheritdoc}
     */
    protected function createOAuthHeaderString($content) {
        $obj = json_decode($content);
        if (empty($obj)) throw new \Exception('Wrong Response from TwitterOAuthMockController: ' . $content);
        return 'Authorization: ' . ucfirst($obj->token_type) . ' ' . $obj->access_token;
    }
}