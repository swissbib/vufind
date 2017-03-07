<?php
/**
 * HoldingActions
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

use Zend\I18n\View\Helper\AbstractTranslatorHelper;

/**
 * Build link for for item actions
 *
 * @category Swissbib_VuFind2
 * @package  View_Helper
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class HoldingActions extends AbstractTranslatorHelper
{
    /**
     * Render action link list
     *
     * @param array  $item      Item
     * @param string $listClass Custom class for list element
     * @param string $recordId  RecordId
     *
     * @return string
     */
    public function __invoke(array $item, $listClass = '', $recordId = '')
    {
        /**
         * RecordLink
         *
         * @var RecordLink $recordLink
         */
        $recordLink = $this->getView()->plugin('recordLink');
        $actions    = [];
        $loginURL   = $this->getView()->url('myresearch-home');

        if (isset($item['backlink'])) {
            if (isset($item['network']) and $item['network'] != 'NATIONALLICENCE') {
                $actions['backlink'] = [
                    'label' => $this->translate('hold_backlink'),
                    'href' => $this->getView()->redirectProtocolWrapper()
                        ->getWrappedURL($item['backlink']),
                    'target' => '_blank'
                ];
            }
        }

        if (isset($item['userActions'])) {
            if (isset($item['userActions']['login'])
                && $item['userActions']['login']
            ) {
                // show different label and sign in
                $actions['sign_in'] = [
                    'label'  => $this->translate('Login'),
                    'href'   => $loginURL,
                ];
            }
            if (isset($item['userActions']['hold'])
                && $item['userActions']['hold']
            ) {
                if (is_array($item['availability'])) {
                    $actions['hold'] = [
                        $itemkey = key($item['availability']),
                        'label' => array_search(
                            'lendable_borrowed',
                            $item['availability'][$itemkey]
                        ) ? $this->translate('Recall This')
                            : $this->translate('hold_place'),
                        'href' => $recordLink->getHoldUrl($item['holdLink'])
                    ];
                } elseif ($item['availability'] === false) {
                    $actions['hold'] = [
                        'label' => $this->translate('hold_place'),
                        'href' => $recordLink->getHoldUrl($item['holdLink'])
                    ];
                }
            }
            if (isset($item['userActions']['shortLoan'])
                && $item['userActions']['shortLoan']
            ) {
                $actions['shortloan'] = [
                    'label' => $this->translate('hold_shortloan'),
                    'href'  => 'javascript:alert(\'Not implemented yet\')'
                ];
            }
            if (isset($item['userActions']['photorequest'])
                && $item['userActions']['photorequest']
            ) {
                $actions['photocopy'] = [
                    'label' => $this->translate('hold_copy'),
                    'href'  => $recordLink->getCopyUrl($item, $recordId),
                ];
            }
            if (isset($item['userActions']['bookingrequest'])
                && $item['userActions']['bookingrequest']
            ) {
                $actions['booking'] = [
                    'label'  => $this->translate('hold_booking'),
                    'href'   => $item['userActions']['bookingRequestLink'],
                    'target' => '_blank',
                ];
            }
        } elseif (isset($item['holdLink'])) {
            $actions['hold'] = [
                'label' => $this->translate('hold_place'),
                'href'  => $recordLink->getHoldUrl($item['holdLink'])
            ];
        }

        if (isset($item['eodlink']) && $item['eodlink']
            && $item['institution'] === 'A125'
        ) {
            $actions['eod'] = [
                'label' => $this->translate('Order_readingroom'),
                'href'  => $item['eodlink']
            ];
        } elseif (isset($item['eodlink']) && $item['eodlink']) {
            $actions['eod'] = [
                'label' => $this->translate('Order_EBook_tooltip'),
                'href'  => $item['eodlink']
            ];
        }

        foreach ($actions as $key => $action) {
            $actions[$key]['class'] = isset($action['class']) ?
                $action['class'] . ' ' . $key : $key;
        }

        $data = [
            'actions'     => $actions,
            'listClass'    => $listClass
        ];

        return $this->getView()->render('Holdings/holding-actions', $data);
    }

    /**
     * Translate message
     *
     * @param String $message    Message
     * @param String $textDomain TextDomain
     * @param String $locale     Locale
     *
     * @return string
     */
    protected function translate($message, $textDomain = 'default', $locale = null)
    {
        return $this->translator->translate($message, $textDomain, $locale);
    }
}
