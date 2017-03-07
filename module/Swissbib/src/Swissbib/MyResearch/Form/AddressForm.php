<?php
/**
 * AddressForm
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
 * @package  MyResearch_Form
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\MyResearch\Form;

use Zend\Form\Annotation;

/**
 * AddressForm
 *
 * @category Swissbib_VuFind2
 * @package  MyResearch_Form
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 *
 * @Annotation\Name("address")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 */
class AddressForm
{
    // @codingStandardsIgnoreStart
    // Annotations might be impossible to comply with CodeSniffer

    /**
     * $z304_address_1
     *
     * @var String
     *
     * @Annotation\Name("z304-address-1")
     * @Annotation\Options({"label":"Address"})
     * @Annotation\Attributes({"type":"text"})
     */
    public $z304_address_1;

    /**
     * $z304_address_2
     *
     * @var String
     *
     * @Annotation\Name("z304-address-2")
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":0, "max":50}})
     * @Annotation\Validator({
     *      "name":"Regex",
     *      "options":{
     *          "pattern":"/^[\w\s\d.\/(),&-]*$/u",
     *          "messages":{"regexNotMatch":"input_contains_disallowed_characters"}
     *      }
     * })
     */
    public $z304_address_2;

    /**
     * $z304_address_3
     *
     * @var String
     *
     * @Annotation\Name("z304-address-3")
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\AllowEmpty(true)
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":0, "max":50}})
     * @Annotation\Validator({
     *     "name":"Regex",
     *     "options":{
     *          "pattern":"/^[\w\s\d.\/(),&-]*$/u",
     *          "messages":{"regexNotMatch":"input_contains_disallowed_characters"}
     *      }
     * })
     */
    public $z304_address_3;

    /**
     * $z304_address_4
     *
     * @var String
     *
     * @Annotation\Name("z304-address-4")
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\AllowEmpty(true)
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":0, "max":50}})
     * @Annotation\Validator({
     *      "name":"Regex",
     *      "options":{
     *          "pattern":"/^[\w\s\d.\/(),&-]*$/u",
     *          "messages":{"regexNotMatch":"input_contains_disallowed_characters"}
     *      }
     * })
     */
    public $z304_address_4;

    /**
     * $z304_address_5
     *
     * @var String
     *
     * @Annotation\Name("z304-address-5")
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\AllowEmpty(true)
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":0, "max":50}})
     * @Annotation\Validator({
     *      "name":"Regex",
     *      "options":{
     *          "pattern":"/^[\w\s\d.\/(),&-]*$/u",
     *          "messages":{"regexNotMatch":"input_contains_disallowed_characters"}
     *      }
     * })
     */
    public $z304_address_5;

    /**
     * $z304_email_address
     *
     * @var String
     *
     * @Annotation\Name("z304-email-address")
     * @Annotation\Options({"label":"Email"})
     * @Annotation\AllowEmpty(true)
     * @Annotation\Type("Zend\Form\Element\Email")
     * @Annotation\Validator({"name":"StringLength", "options":{"min":0, "max":60}})
     * @Annotation\Validator({"name":"EmailAddress"})
     * @Annotation\ErrorMessage("Email address is invalid")
     */
    public $z304_email_address;

    /**
     * $z304_telephone_1
     *
     * @var String
     *
     * @Annotation\Name("z304-telephone-1")
     * @Annotation\Options({"label":"Phone Number 1"})
     * @Annotation\Attributes({"type":"tel"})
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":0, "max":30}})
     * @Annotation\Validator({
     *      "name":"Regex",
     *      "options":{
     *          "pattern":"/^[\w\s\d(),\/+.-]*$/u",
     *          "messages":{"regexNotMatch":"input_contains_disallowed_characters"}
     *      }
     * })
     */
    public $z304_telephone_1;

    /**
     * $z304_telephone_2
     *
     * @var String
     *
     * @Annotation\Name("z304-telephone-2")
     * @Annotation\Options({"label":"Phone Number 2"})
     * @Annotation\Attributes({"type":"tel"})
     * @Annotation\AllowEmpty(true)
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":0, "max":30}})
     * @Annotation\Validator({
     *      "name":"Regex",
     *      "options":{
     *          "pattern":"/^[\w\s\d(),\/+.-]*$/u",
     *          "messages":{"regexNotMatch":"input_contains_disallowed_characters"}
     *      }
     * })
     */
    public $z304_telephone_2;

    /**
     * $z304_telephone_3
     *
     * @var String
     *
     * @Annotation\Name("z304-telephone-3")
     * @Annotation\Options({"label":"Phone Number 3"})
     * @Annotation\Attributes({"type":"tel"})
     * @Annotation\AllowEmpty(true)
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":0, "max":30}})
     * @Annotation\Validator({
     *      "name":"Regex",
     *      "options":{
     *          "pattern":"/^[\w\s\d(),\/+.-]*$/u",
     *          "messages":{"regexNotMatch":"input_contains_disallowed_characters"}
     *      }
     * })
     */
    public $z304_telephone_3;

    /**
     * $z304_telephone_4
     *
     * @var String
     *
     * @Annotation\Name("z304-telephone-4")
     * @Annotation\Options({"label":"Phone Number 4"})
     * @Annotation\Attributes({"type":"tel"})
     * @Annotation\AllowEmpty(true)
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":0, "max":30}})
     * @Annotation\Validator({
     *      "name":"Regex",
     *      "options":{
     *          "pattern":"/^[\w\s\d(),\/+.-]*$/u",
     *          "messages":{"regexNotMatch":"input_contains_disallowed_characters"}
     *      }
     * })
     */
    public $z304_telephone_4;

    // @codingStandardsIgnoreEnd
}
