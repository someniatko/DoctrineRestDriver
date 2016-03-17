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

namespace Circle\DoctrineRestDriver\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Mock controller for testing twitter oauth authentication process
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 */
class TwitterOAuthMockController extends Controller {

    /**
     * Mock action for creating a token
     *
     * @param  Request  $request
     * @return Response
     */
    public function tokenAction(Request $request) {
        if ($request->headers->get('Authorization') !== 'Basic Y2lyY2xlVXNlcjpteVNlY3JldFBhc3N3b3Jk') return new Response('Wrong Authentication credentials. Expected Basic Y2lyY2xlVXNlcjpteVNlY3JldFBhc3N3b3Jk, got ' . $request->headers->get('Authorization'), 403);

        return new Response(json_encode([
            'token_type'   => 'bearer',
            'access_token' => 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'
        ]));
    }
}
