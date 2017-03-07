<?php
 /**
 * QueryBuilder
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 9/20/13
 * Time: 12:51 PM
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category Swissbib_VuFind2
 * @package  VuFindSearch_Backend_Solr
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\VuFindSearch\Backend\Solr;

use VuFindSearch\Backend\Solr\QueryBuilder as VFBuilder;

/**
 * QueryBuilder
 *
 * @category Swissbib_VuFind2
 * @package  VuFindSearch_Backend_Solr
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org  Main Page
 */
class QueryBuilder extends VFBuilder
{
    /**
     * DisMaxSearchFields
     *
     * @var array
     */
    protected $disMaxSearchFields = [];

    /**
     * Constructor
     *
     * @param array $specs Specs
     */
    public function __construct(array $specs = [])
    {
        parent::__construct($specs);

        if (array_key_exists("allfields", $this->specs)
            && array_key_exists(
                "DismaxFields",  $this->specs["allfields"]->toArray()
            )
        ) {
            $tempArray = $this->specs["allfields"]->toArray();
            $this->disMaxSearchFields = $tempArray["DismaxFields"];
        }

        $this->disMaxSearchFields = array_map(
            function ($item) {

                if (strpos($item, "^") > 0) {
                    return substr($item, 0, strpos($item, "^"));

                } else {
                    return $item;
                }

            }, $this->disMaxSearchFields
        );

        //this search field isn't defined in searchspec
        $this->disMaxSearchFields[] = "hierarchy_parent_id";
        $this->disMaxSearchFields[] = "id";
    }

    /**
     * PrepareForLuceneSyntax
     *
     * @param String $input Input
     *
     * @return mixed
     */
    protected function prepareForLuceneSyntax($input)
    {

        $alreadyPrepared = parent::prepareForLuceneSyntax($input);

        preg_match_all("/(?P<name>\w+?:|[ ]:)/", $input, $matches);

        if (count($matches["name"] > 0)) {

            foreach ($matches["name"] as $fieldNameWithColon) {

                $fieldNameNoColon = substr(
                    $fieldNameWithColon, 0, strpos($fieldNameWithColon, ":")
                );

                if (!in_array($fieldNameNoColon, $this->disMaxSearchFields)) {
                    $alreadyPrepared = str_replace(
                        $fieldNameWithColon,
                        $fieldNameNoColon . " ",
                        $alreadyPrepared
                    );
                }
            };
        }

        //$alreadyPrepared = str_replace('-', ' ', $alreadyPrepared);
        //$alreadyPrepared = str_replace(array("-","="), ' ', $alreadyPrepared);
        $alreadyPrepared = str_replace(["="], ' ', $alreadyPrepared);

        return $alreadyPrepared;
    }
}
