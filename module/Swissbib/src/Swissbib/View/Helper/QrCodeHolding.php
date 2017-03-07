<?php
/**
 * QrCodeHolding
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
 * @author   Nicolas Karrer <nkarrer@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\View\Helper;

use Zend\I18n\View\Helper\AbstractTranslatorHelper;

/**
 * Build holding qr code url
 *
 * @category Swissbib_VuFind2
 * @package  View_Helper
 * @author   Nicolas Karrer <nkarrer@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class QrCodeHolding extends AbstractTranslatorHelper
{
    /**
     * QrCodeHelper
     *
     * @var QrCode
     */
    protected $qrCodeHelper;

    /**
     * Build CRCode image source url for holding
     *
     * @param Array  $item        Item
     * @param String $recordTitle RecordTitle
     *
     * @return String
     */
    public function __invoke(array $item, $recordTitle = '')
    {
        if (!$this->qrCodeHelper) {
            $this->qrCodeHelper = $this->getView()->plugin('qrCode');
        }

        $data = [];

        if (!empty($recordTitle)) {
            $data[] = $recordTitle;
        }
        if (!empty($item['institution'])) {
            $data[] = $this->translator->translate(
                $item['institution'], 'institution'
            );
        }
        if (!empty($item['locationLabel'])) {
            $data[] = $item['locationLabel'];
        }
        if (!empty($item['signature'])) {
            $data[] = $item['signature'];
        }

        $text        = implode(', ', $data);
        $qrCodeUrl    = $this->qrCodeHelper->source($text, 250, false);

        return $this->getView()->render(
            'Holdings/qr-code',
            [
                 'item' => $item,
                 'url'  => $qrCodeUrl,
                 'text' => $text
            ]
        );
    }
}
