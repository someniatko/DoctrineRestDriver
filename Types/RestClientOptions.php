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

use Circle\DoctrineRestDriver\Validation\Assertions;

/**
 * RestClientOptions type
 *
 * @author    Tobias Hauck <tobias@circle.ai>
 * @copyright 2015 TeeAge-Beatz UG
 */
class RestClientOptions extends \ArrayObject {
    use Assertions;

    /**
     * Request constructor
     *
     * @param array $options
     */
    public function __construct(array $options) {
        $this->validate($options);

        parent::__construct((array) new CurlOptions($options['driverOptions']));
    }

    /**
     * validates the given input
     *
     * @param  array $params
     * @return void
     */
    private function validate(array $params) {
        $this->assertHashMap('params', $params);
        $this->assertHashMapEntryExists('params', $params, 'driverOptions');
        $this->assertHashMap('params["driverOptions"]', $params['driverOptions']);
    }
}