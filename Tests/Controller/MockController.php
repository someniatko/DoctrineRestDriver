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

namespace Circle\DoctrineRestDriverBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Mock controller for functional testing
 *
 * @author    Tobias Hauck <tobias.hauck@teeage-beatz.de>
 * @copyright 2015 TeeAge-Beatz UG
 */
class MockController extends Controller {

    /**
     * @Route("/products/{id}")
     * @Method({"GET"})
     *
     * Mock action for testing get
     *
     * @param  string   $id
     * @return Response
     */
    public function getAction($id) {
        if ($id != 1) return new Response('', 404);

        return new Response(json_encode([
            'id'    => 1,
            'name'  => 'MyName',
            'value' => 'MyValue',
        ]));
    }

    /**
     * Mock action for testing getAll
     *
     * @return Response
     *
     * @Route("/products")
     * @Method({"GET"})
     */
    public function getAllAction() {
        return new Response(json_encode([
            [
                'id'    => 1,
                'name'  => 'MyName',
                'value' => 'MyValue',
            ],
            [
                'id'    => 2,
                'name'  => 'NextName',
                'value' => 'NextValue',
            ]
        ]));
    }

    /**
     * Mock action for testing post
     *
     * @param  Request  $request
     * @return Response
     *
     * @Route("/products")
     * @Method({"POST"})
     */
    public function postAction(Request $request) {
        $payload = json_decode($request->getContent());

        return new Response(json_encode([
            'id'    => 1,
            'name'  => $payload->name,
            'value' => $payload->value,
        ]));
    }

    /**
     * Mock action for testing put
     *
     * @param  string   $id
     * @return Response
     *
     * @Route("/products/{id}")
     * @Method({"PUT"})
     */
    public function putAction($id) {
        if ($id != 1) return new Response('', 404);

        return new Response(json_encode([
            'id'    => 1,
            'name'  => 'MyName',
            'value' => 'MyValue',
        ]));
    }

    /**
     * Mock action for testing delete
     *
     * @param  string   $id
     * @return Response
     *
     * @Route("/products/{id}")
     * @Method({"DELETE"})
     */
    public function deleteAction($id) {
        if ($id != 1) return new Response('', 404);

        return new Response('', 204);
    }
}
