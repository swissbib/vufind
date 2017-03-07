<?php
/**
 * CopyForm
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 1/2/13
 * Time: 4:09 PM
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
 * @package  Record_Form
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\Record\Form;

use Zend\Form\Annotation;

/**
 * AddressForm
 *
 * @category Swissbib_VuFind2
 * @package  Record_Form
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 *
 * @Annotation\Name("copy")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 */
class CopyForm
{
    /**
     * $pickup_location
     *
     * @var String
     *
     * @Annotation\Name("pickup-location")
     * @Annotation\Options({"label":"pick_up_location"})
     * @Annotation\Type("Zend\Form\Element\Select")
     */
    public $pickup_location;

    /**
     * $sub_author
     *
     * @var String
     *
     * @Annotation\Name("sub-author")
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Author"})
     * @Annotation\AllowEmpty(true)
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":0, "max":50}})
     */
    public $sub_author;

    /**
     * $sub_title
     *
     * @var String
     *
     * @Annotation\Name("sub-title")
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Title"})
     * @Annotation\AllowEmpty(true)
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":0, "max":100}})
     */
    public $sub_title;

    /**
     * $pages
     *
     * @var String
     *
     * @Annotation\Name("pages")
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"pages"})
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":0, "max":30}})
     */
    public $pages;

    /**
     * $note1
     *
     * @var String
     *
     * @Annotation\Name("note1")
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"note1"})
     * @Annotation\AllowEmpty(true)
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":0, "max":50}})
     */
    public $note1;

    /**
     * $note2
     *
     * @var String
     *
     * @Annotation\Name("note2")
     * @Annotation\Attributes({"type":"textarea"})
     * @Annotation\Options({"label":"kommentar"})
     * @Annotation\AllowEmpty(true)
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":0, "max":50}})
     */
    public $note2;
}
