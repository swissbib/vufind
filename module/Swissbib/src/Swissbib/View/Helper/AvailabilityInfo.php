<?php
/**
 * AvailabilityInfo
 *
 * PHP version 7
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category Swissbib_VuFind
 * @package  View_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * Show availability infos
 *
 * @category Swissbib_VuFind
 * @package  View_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class AvailabilityInfo extends AbstractHelper
{
    /**
     * Expected status codes
     */

    /**
     * Generell ausleihbar und vorhandene Exemplare
     */
    const LENDABLE_AVAILABLE = "lendable_available";

    /**
     * Generell ausleihbar, jedoch bereits ausgeliehene Exemplare
     */
    const LENDABLE_BORROWED = "lendable_borrowed";

    /**
     * Kurzausleihe (1-7 Tage)
     */
    const LENDABLESHORT = "lendableShort";

    /**
     * Vor Ort einsehbare Exemplare (Lesesaal)
     */
    const USE_ON_SITE = "use-on-site";

    /**
     * Siehe Bibliotheksinformation
     */
    const LIBRARYINFO = "libraryInfo";

    /**
     * Informationsabruf über das lokale System (fallback)
     */
    const LOOK_ON_SITE = "lookOnSite";

    /**
     * Nicht einsehbar, extern ausgestellt (mit Datum bis)
     */
    const EXHIBITION = "exhibition";

    /**
     * InProcess
     */
    const INPROCESS = "inProcess";

    /**
     * By now only for ETH, could be enhanced for other library systems
     * (labels for LoanStatus needed!)
     */
    const ONLINE_AVAILABLE = "onlineAvailable";

    /**
     * Vermisst, in Reparatur, abbestellt: Exemplar für Benutzer verloren
     */
    const UNAVAILABLE = "unavailable";

    /**
     * Substitute
     */
    const SUBSTITUTE = "substitute";

    /**
     * Nur für Kopienbestellung verfügbar
     */
    const PHOTOCOPY = "photocopy";

    /**
     * Convert availability info into html string
     *
     * @param Boolean|Array $availability Availability
     * @param String        $institution  Institution
     *
     * @return String
     */
    public function __invoke($availability, $institution = "")
    {
        $escapedTranslation = $this->getView()->plugin('transEsc');

        /* Availability always contains an associative  array with only 'one' key
         * (the barcode of the item)
         * (this method is called for every single item)
         * the barcode references an additional array (derived from json) which
         * contains the so called statusfield
         * the value of the statusfield is part of the translation files
         */
        if (is_array($availability)) {
            $statusfield = self::LOOK_ON_SITE;
            $borrowingInfo = [];

            foreach ($availability as $barcode => $availinfo) {
                $statusfield = $availinfo["statusfield"];

                if (isset($availinfo["borrowingInformation"])) {
                    $borrowingInfo = $availinfo["borrowingInformation"];
                }
            }

            switch ($statusfield) {
            case self::LENDABLE_AVAILABLE:

                $info = "<div class='availability fa-check'>&nbsp;</div>";
                break;
            case self::LENDABLE_BORROWED:

                unset($borrowingInfo['due_hour']);
                $info = "<div class='availability fa-ban'>";

                if (isset($borrowingInfo['due_date'])) {
                    if ($borrowingInfo['due_date'] === 'on reserve') {
                        $info .= $escapedTranslation('On Reserve') . " (" .
                            $borrowingInfo['no_requests'] . ")";
                    } elseif ($borrowingInfo['due_date'] === 'claimed returned') {
                        $info .= $escapedTranslation('Claimed Returned');
                    } elseif ($borrowingInfo['due_date'] === 'lost') {
                        $info .= $escapedTranslation('Lost');
                    } elseif ($borrowingInfo['due_date'] === 'on hold') {
                        $info .= $escapedTranslation('on_hold') . "<div>" .
                            $escapedTranslation('no_requests') . "&nbsp;" .
                            $borrowingInfo['no_requests'] . "</div>";
                    } else {
                        foreach ($borrowingInfo as $key => $value) {
                            if (strcmp(trim($value), "") != 0) {
                                $info .= "<div>" . $escapedTranslation($key)
                                    . "&nbsp;" .
                                    $value . "</div>";
                            }
                        }
                    }
                }

                $info .= "</div>";

                break;

            case self::LENDABLESHORT:
                if (!empty($borrowingInfo['due_date'])) {
                    unset($borrowingInfo['due_hour']);
                    $infotext = $escapedTranslation($statusfield);
                    $info = "<div class='availability fa-ban'><div>" . "$infotext" .
                        "</div>";

                    if ($borrowingInfo['due_date'] === 'on reserve') {
                        $info .= $escapedTranslation('On Reserve') . " (" .
                            $borrowingInfo['no_requests'] . ")";

                    // @codingStandardsIgnoreStart
                    } elseif ($borrowingInfo['due_date'] ===
                        'claimed returned') {
                        // @codingStandardsIgnoreEnd

                        $info .= $escapedTranslation('Claimed Returned');
                    } elseif ($borrowingInfo['due_date'] === 'lost') {
                        $info .= $escapedTranslation('Lost');
                    } elseif ($borrowingInfo['due_date'] === 'on hold') {
                        $info .= $escapedTranslation('on_hold') . "<div>" .
                            $escapedTranslation('no_requests') . "&nbsp;" .
                            $borrowingInfo['no_requests'] . "</div>";
                    } else {
                        foreach ($borrowingInfo as $key => $value) {
                            if (strcmp(trim($value), "") != 0) {
                                $info .= "<div>" . $escapedTranslation($key) .
                                    "&nbsp;" . $value . "</div>";
                            }
                        }
                    }
                    $info .= "</div>";
                } elseif (empty($borrowingInfo['due_date'])) {
                    $infotext = $escapedTranslation($statusfield);
                    $info = "<div class='availability fa-check'><div>" .
                        "$infotext" . "</div></div>";
                }

                break;

            case self::USE_ON_SITE:

                if (!empty($borrowingInfo['due_date'])) {
                    unset($borrowingInfo['due_hour']);
                    $infotext = $escapedTranslation($statusfield);
                    $info = "<div class='availability fa-ban'><div>" .
                        "$infotext" . "</div>";

                    if ($borrowingInfo['due_date'] === 'on reserve') {
                        $info .= $escapedTranslation('On Reserve') . " (" .
                            $borrowingInfo['no_requests'] . ")";

                    // @codingStandardsIgnoreStart
                    } elseif ($borrowingInfo['due_date'] ===
                        'claimed returned') {
                        // @codingStandardsIgnoreEnd

                        $info .= $escapedTranslation('Claimed Returned');
                    } elseif ($borrowingInfo['due_date'] === 'lost') {
                        $info .= $escapedTranslation('Lost');
                    } elseif ($borrowingInfo['due_date'] === 'on hold') {
                        $info .= $escapedTranslation('on_hold') . "<div>" .
                            $escapedTranslation('no_requests') . "&nbsp;" .
                            $borrowingInfo['no_requests'] . "</div>";
                    } else {
                        foreach ($borrowingInfo as $key => $value) {
                            if (strcmp(trim($value), "") != 0) {
                                $info .= "<div>" . $escapedTranslation($key) .
                                    "&nbsp;" . $value . "</div>";
                            }
                        }
                    }
                    $info .= "</div>";
                } elseif (empty($borrowingInfo['due_date'])) {
                    $infotext = $escapedTranslation($statusfield);
                    $info = "<div class='availability fa-check'><div>" .
                        "$infotext" . "</div></div>";
                }

                break;

            case self::LIBRARYINFO:
                if (!empty($borrowingInfo['due_date'])) {
                    unset($borrowingInfo['due_hour']);
                    $infotext = $escapedTranslation($statusfield);
                    $info = "<div class='availability fa-ban'><div>" .
                        "$infotext" . "</div>";

                    if ($borrowingInfo['due_date'] === 'on reserve') {
                        $info .= $escapedTranslation('On Reserve') . " (" .
                            $borrowingInfo['no_requests'] . ")";

                    // @codingStandardsIgnoreStart
                    } elseif ($borrowingInfo['due_date'] ===
                        'claimed returned') {
                        // @codingStandardsIgnoreEnd

                        $info .= $escapedTranslation('Claimed Returned');
                    } elseif ($borrowingInfo['due_date'] === 'lost') {
                        $info .= $escapedTranslation('Lost');
                    } elseif ($borrowingInfo['due_date'] === 'on hold') {
                        $info .= $escapedTranslation('on_hold') . "<div>" .
                            $escapedTranslation('no_requests') . "&nbsp;" .
                            $borrowingInfo['no_requests'] . "</div>";
                    } else {
                        foreach ($borrowingInfo as $key => $value) {
                            if (strcmp(trim($value), "") != 0) {
                                $info .= "<div>" . $escapedTranslation($key) .
                                    "&nbsp;" . $value . "</div>";
                            }
                        }
                    }
                    $info .= "</div>";
                } elseif (empty($borrowingInfo['due_date'])) {
                    $infotext = $escapedTranslation($statusfield);
                    $info = "<div>" . "$infotext" . "</div>";
                }

                break;

            case self::LOOK_ON_SITE:

                $infotext = $escapedTranslation($statusfield);
                $info = "<div class='availability fa-question'><div>" .
                    "$infotext" . "</div></div>";
                switch ($institution) {
                case 'Z998':
                    $infotext = $escapedTranslation('holdings_noitems');
                    $info = "<div>" . "$infotext" . "</div>";
                    break;
                }
                break;
            case self::EXHIBITION:

                unset($borrowingInfo['due_hour']);
                $infotext = $escapedTranslation($statusfield);
                $info = "<div class='availability fa-ban'>Ausstellung";

                if ($borrowingInfo['due_date'] === 'on reserve') {
                    $info .= $escapedTranslation('On Reserve') . " (" .
                        $borrowingInfo['no_requests'] . ")";
                } else {
                    foreach ($borrowingInfo as $key => $value) {
                        if (strcmp(trim($value), "") != 0) {
                            $info .= "<div>" . $escapedTranslation($key) .
                                "&nbsp;" . $value . "</div>";
                        }
                    }
                }

                $info .= "</div>";

                break;
            case self::INPROCESS:
                if (!empty($borrowingInfo['due_date'])) {
                    unset($borrowingInfo['due_hour']);
                    $infotext = $escapedTranslation($statusfield);
                    $info = "<div class='availability fa-ban'><div>" . "$infotext" .
                        "</div>";

                    if ($borrowingInfo['due_date'] === 'on reserve') {
                        $info .= $escapedTranslation('On Reserve') . " (" .
                            $borrowingInfo['no_requests'] . ")";
                    } else {
                        foreach ($borrowingInfo as $key => $value) {
                            if (strcmp(trim($value), "") != 0) {
                                $info .= "<div>" . $escapedTranslation($key) .
                                    "&nbsp;" . $value . "</div>";
                            }
                        }
                    }
                    $info .= "</div>";
                } elseif (empty($borrowingInfo['due_date'])) {
                    $infotext = $escapedTranslation($statusfield);
                    $info = "<div class='availability fa-check'><div>" .
                        "$infotext" . "</div></div>";
                }
                break;
            case self::ONLINE_AVAILABLE:

                //do something special for online resources
                // (dedicated icon and / or text?)
                $info = $escapedTranslation($statusfield);
                break;
            case self::UNAVAILABLE:
            case self::SUBSTITUTE:

                $infotext = $escapedTranslation($statusfield);
                $info = "<div class='availability fa-ban'>" . "$infotext" . "</div>";
                break;
            case self::PHOTOCOPY:

                $infotext = $escapedTranslation($statusfield);
                $info = "<div class='availability fa-check'>" . "$infotext" .
                    "</div>";
                break;
            default:
                /**
                 * Any other value defined in the availability service
                 * should be translated in the language files of VuFind
                 * (local/languages/)
                 */
                $info = $escapedTranslation($statusfield);
            }
        } else {
            switch ($institution) {
            case 'NATIONALLICENCE':
                $info = $escapedTranslation('onlineAvailableForSwiss');
                break;
            case 'RETROS':
            case 'BORIS':
            case 'EDOC':
            case 'ECOD':
            case 'ALEXREPO':
                $info = $escapedTranslation('onlineAvailable');
                break;
            default:
                $info = $escapedTranslation('no_ava_info');
            }
        }

        return $info;
    }
}
