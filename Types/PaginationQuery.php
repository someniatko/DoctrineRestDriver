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

/**
 * Handles creation of pagination related http headers
 *
 * @author Rob Treacy <robert.treacy@thesalegroup.co.uk>
 * @copyright 2016
 */
class PaginationQuery {

    const DEFAULT_PER_PAGE_PARAM = 'per_page';
    const DEFAULT_PAGE_PARAM     = 'page';
    /**
     * Returns Limit and Offset headers
     *
     * @param  array $tokens
     * @return string
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public static function create(array $tokens, $perPageParam = self::DEFAULT_PER_PAGE_PARAM, $pageParam = self::DEFAULT_PAGE_PARAM) {
        HashMap::assert($tokens, 'tokens');

        if (empty($tokens['LIMIT'])) return [];
        
        $perPage = $tokens['LIMIT']['rowcount'];
        $offset  = isset($tokens['LIMIT']['offset']) ? (int) $tokens['LIMIT']['offset'] : 0;
        
        $page = (int) floor(($offset + $perPage) / $perPage);

        $parameters = array(
            $perPageParam => $tokens['LIMIT']['rowcount'],
            $pageParam    => $page,
        );
        
        return $parameters;
    }
}
