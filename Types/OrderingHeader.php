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
use Circle\DoctrineRestDriver\Validation\Assertions;

/**
 * OrderHttpHeader type
 *
 * @author    Djane Rey Mabelin <thedjaney@gmail.com>
 * @copyright 2016
 */
class OrderingHeader {

    /**
     * Creates a http header using ORDER
     * clause of the parsed sql tokens
     *
     * @param  array $tokens
     * @return string
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public static function create(array $tokens) {
        Assertions::assertHashMap('tokens', $tokens);
        $arr = [];
        if(isset($tokens['ORDER'])){
            $orderQueryArr = [];
            foreach($tokens['ORDER'] as $order){
                $query = null;
                if( isset($order['no_quotes']['parts'][1]) ){
                    $query = $order['no_quotes']['parts'][1];
                }
                if($query && isset($order['direction'])){
                    $query.=' '.$order['direction'];
                }
                $orderQueryArr[] = $query;
            }
            $arr[] = 'Query-Order: '.implode(',',$orderQueryArr);
        }
        return $arr;
    }
}
