<?php
/**
 * Factory for view helpers.
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
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
 * @package  View_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\View\Helper;

use Zend\I18n\Translator\Translator;
use Zend\I18n\View\Helper\Translate;
use Zend\ServiceManager\ServiceManager;

/**
 * Factory for swissbib specific view helpers.
 *
 * @category Swissbib_VuFind2
 * @package  View_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Factory
{
    /**
     * GetInstitutionSorter
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return InstitutionSorter
     */
    public static function getInstitutionSorter(ServiceManager $sm)
    {
        /**
         * RelationConfig
         *
         * @var Config $relationConfig
         */
        $relationConfig = $sm->get('VuFind\Config\PluginManager')
            ->get('libadmin-groups');
        $institutionList = [];

        if ($relationConfig->count() !== null) {
            $institutionList = array_keys($relationConfig->institutions->toArray());
        }

        return new InstitutionSorter($institutionList);
    }

    /**
     * GetFavoriteInstitutionExtractor
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return ExtractFavoriteInstitutionsForHoldings
     */
    public static function getFavoriteInstitutionsExtractor(ServiceManager $sm)
    {
        /**
         * GetFavoriteInstitutionsExtractor
         *
         * @var \Swissbib\Favorites\Manager $favoriteManager
         */
        $favoriteManager = $sm->get('Swissbib\FavoriteInstitutions\Manager');
        $userInstitutionCodes = $favoriteManager->getUserInstitutions();

        return new ExtractFavoriteInstitutionsForHoldings($userInstitutionCodes);
    }

    /**
     * GetInstitutionsAsDefinedFavorites
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return InstitutionDefinedAsFavorite
     */
    public static function getInstitutionsAsDefinedFavorites(ServiceManager $sm)
    {
        $dataSource = $sm->get('Swissbib\FavoriteInstitutions\DataSource');
        $tInstitutions = $dataSource->getFavoriteInstitutions();

        return new InstitutionDefinedAsFavorite($tInstitutions);
    }

    /**
     * IsFavoriteInstitutionHelper
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return IsFavoriteInstitution
     */
    public static function isFavoriteInstitutionHelper(ServiceManager $sm)
    {
        /**
         * FavoritesManager
         *
         * @var \Swissbib\Favorites\Manager $favoriteManager
         */
        $favoriteManager = $sm->get('Swissbib\FavoriteInstitutions\Manager');
        $userInstitutionCodes = $favoriteManager->getUserInstitutions();

        return new IsFavoriteInstitution($userInstitutionCodes);
    }

    /**
     * GetDomainURLHelper
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return DomainURL
     */
    public static function getDomainURLHelper(ServiceManager $sm)
    {
        return new DomainURL($sm->get('Request'));
    }

    /**
     * GetConfig
     *
     * @param ServiceManager $sm ServiceManager
     *
     * @return Config
     */
    public static function getConfig(ServiceManager $sm)
    {
        return new  Config($sm);
    }

    public static function getTranslator(ServiceManager $sm)
    {
        $translator = new Translate();
        $translator->setTranslator(new Translator());
        return $translator;
    }
}
