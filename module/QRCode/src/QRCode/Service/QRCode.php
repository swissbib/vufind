<?php
/**
 * ZF2 module definition for the VuFind application
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
 * @category QRCode_VuFind2
 * @package  QRCode_Service
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace QRCode\Service;

/**
 * QRCode service for Zend Framework 2
 *
 * @category QRCode_VuFind2
 * @package  QRCode_Service
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class QRCode
{
    /**
     * Properties for the qrcode
     *
     * @var Array
     */
    protected $properties = [];
    
    /**
     * The Final Endpoint
     *
     * @var string The final endpoint
     */
    protected $endpoint = null;
    
    /**
     * The Start endpoint
     *
     * @var string
     */
    const END_POINT = 'chart.googleapis.com/chart?';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setCharset();
        $this->setCorrectionLevel();
        $this->setTypeChart();
    }
    
    /**
     * Is Http?
     *
     * @return QRCode
     */
    public function isHttp()
    {
        $this->endpoint = 'http://' . self::END_POINT;

        return $this;
    }

    /**
     * Is Https?
     *
     * @return QRCode
     */
    public function isHttps()
    {
        $this->endpoint = 'https://' . self::END_POINT;

        return $this;
    }

    /**
     * Set chart type, here 'qr' is default chart type, mainly for qrcode
     *
     * @param String $chart Chart Type
     *
     * @return QRCode
     */
    public function setTypeChart($chart = 'qr')
    {
        $this->properties['cht'] = $chart;
        return $this;
    }

    /**
     * Returns the chart type
     *
     * @return String 
     */
    public function getTypeChart()
    {
        return $this->properties['cht'];
    }

    /**
     * Get the link for image of qrcode
     *
     * @return String
     */
    public function getResult()
    {
        return $this->endpoint . http_build_query($this->properties);
    }

    /**
     * Set dimensions (width, height) of image
     *
     * @param Integer $w Width of image
     * @param Integer $h Height of image
     *
     * @throws \InvalidArgumentException
     *
     * @return QRCode
     */
    public function setDimensions($w, $h)
    {
        if (is_int($w) && is_int($h)) {
            $this->properties['chs'] = "{$w}x{$h}";
        } else {
            throw new \InvalidArgumentException(
                'The parameter $w and $h must be integer type'
            );
        }

        return $this;
    }

    /**
     * Return the dimensions of image
     *
     * @return String
     */
    public function getDimensions()
    {
        return $this->properties['chs'];
    }

    /**
     * Set the charset of content data. Default is 'UTF-8'
     *
     * @param String $charset charset of content data
     *
     * @return QRCode
     */
    public function setCharset($charset = 'UTF-8')
    {
        $this->properties['choe'] = $charset;
        return $this;
    }

    /**
     * Return the charset of content data
     *
     * @return String
     */
    public function getCharset()
    {
        return $this->properties['choe'];
    }
    
    /**
     * Set level of loss of content and margin of image
     *
     * @param String  $cl Level of loss
     * @param Integer $m  Margin of image
     *
     * @return QRCode
     */
    public function setCorrectionLevel($cl = 'L',$m = 0)
    {
        $this->properties['chld'] = "{$cl}|{$m}";
        return $this;
    }
    
    /**
     * Return level of loss of content and margin of image
     *
     * @return String
     */
    public function getCorrectionLevel()
    {
        return $this->properties['chld'];
    }
    
    /**
     * Set content data in urlencode format
     *
     * @param String $data Content
     *
     * @return QRCode
     */
    public function setData($data)
    {
        $this->properties['chl'] = urlencode($data);

        return $this;
    }

    /**
     * Return the content data in urldecode format.
     *
     * @return String
     */
    public function getData()
    {
        return urldecode($this->properties['chl']);
    }
}
