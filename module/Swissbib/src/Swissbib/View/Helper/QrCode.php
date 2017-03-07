<?php
/**
 * QrCode
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

use Zend\View\Helper\AbstractHelper;
use Swissbib\CRCode\QrCodeService;

/**
 * Build a qr code link or image
 *
 * @category Swissbib_VuFind2
 * @package  View_Helper
 * @author   Nicolas Karrer <nkarrer@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class QrCode extends AbstractHelper
{
    /**
     * QRCodeService
     *
     * @var QRCodeService
     */
    protected $qrCodeService;

    /**
     * Initialize with service
     *
     * @param QRCodeService $qrCodeService QRCodeService
     */
    public function __construct(QRCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;

            // set defaults
        $this->qrCodeService->setDimensions(100, 100);
        $this->qrCodeService->isHttp();
    }

    /**
     * Read config from options array
     * Return a new copy of the qr code service
     *
     * @param Array $options Options
     *
     * @return QRCodeService
     */
    protected function build(array $options)
    {
        $qrCode = clone $this->qrCodeService;

        if (isset($options['data'])) {
            $encode = isset($options['encodeData']) ? $options['encodeData'] : true;

            $qrCode->setData($options['data'], $encode);
        }
        if (isset($options['charset'])) {
            $qrCode->setCharset($options['charset']);
        }
        if (isset($options['correctionLevel'])) {
            $qrCode->setCorrectionLevel($options['correctionLevel']);
        }
        if (isset($options['dimensions'])) {
            if (is_array($options['dimensions'])) {
                list($with,$height) = $options['dimensions'];
            } else {
                list($with,$height) = explode(',', $options['dimensions']);
            }
            $qrCode->setDimensions($with, $height);
        }
        if (isset($options['https'])) {
            $options['https'] ? $qrCode->isHttps() : $qrCode->isHttp();
        }

        return $qrCode;
    }

    /**
     * Get URl only
     *
     * @param Array $options Options
     *
     * @return String
     */
    public function url(array $options)
    {
        return $this->build($options)->getResult();
    }

    /**
     * Img
     *
     * @param array $options Options
     *
     * @return string
     */
    public function img(array $options)
    {
        $qrCode = $this->build($options);
        $class    = isset($options['class']) && $options['class'] ?
            ' class="' . $options['class'] . '"' : '';
        $title    = isset($options['title']) && $options['title'] ?
            ' title="' . $options['title'] . '"' : '';

        list($w,$h)    = explode('x', $qrCode->getDimensions());

        return '<img src="' . $qrCode->getResult() .
            '" width="' . $w . '" height="' . $h . '"' . $class . $title . '>';
    }

    /**
     * Simplified version of img
     * Get full image tag
     *
     * @param String  $text   Text
     * @param Integer $size   Size
     * @param Boolean $encode Encode
     *
     * @return String
     */
    public function image($text, $size, $encode = true)
    {
        return $this->img(
            [
                'data'       => $text,
                'encodeData' => !!$encode,
                'dimensions' => [$size, $size]
            ]
        );
    }

    /**
     * Simplified version of url
     *
     * @param String  $text   Text
     * @param Integer $size   Size
     * @param Boolean $encode Encode
     *
     * @return String
     */
    public function source($text, $size, $encode = true)
    {
        return $this->url(
            [
                'data'       => $text,
                'encodeData' => !!$encode,
                'dimensions' => [$size, $size]
            ]
        );
    }
}
