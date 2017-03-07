<?php
/**
 * FeedbackForm
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 10/12/15
 * Time: 11:23
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
 * @package  Feedback_Form
 * @author   Markus Mächler <markus.maechler@bithost.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\Feedback\Form;

use Zend\Form\Annotation;

/**
 * FeedbackForm
 *
 * @category Swissbib_VuFind2
 * @package  Feedback_Form
 * @author   Markus Mächler <markus.maechler@bithost.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class FeedbackForm
{
    // @codingStandardsIgnoreStart
    // Annotations might be impossible to comply with CodeSniffer

    /**
     * $questionType
     *
     * @var String
     *
     * @Annotation\Name("questionType")
     * @Annotation\Options({
     *     "label":"feedback.form.questionType",
     *     "value_options":{
     *          "feedback.form.questionType.0":"feedback.form.questionType.0.value",
     *          "feedback.form.questionType.1":"feedback.form.questionType.1.value",
     *          "feedback.form.questionType.2":"feedback.form.questionType.2.value"
     *     }
     * })
     * @Annotation\Attributes({"type":"radio"})
     */
    public $questionType;

    /**
     * $question
     *
     * @var String
     *
     * @Annotation\Name("question")
     * @Annotation\Options({"label":"feedback.form.question"})
     * @Annotation\Required(true)
     * @Annotation\Attributes({"type":"textarea"})
     */
    public $question;

    /**
     * $name
     *
     * @var String
     *
     * @Annotation\Name("name")
     * @Annotation\Options({"label":"feedback.form.name"})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":0, "max":50}})
     */
    public $name;

    /**
     * $userNumber
     *
     * @var String
     *
     * @Annotation\Name("userNumber")
     * @Annotation\Options({"label":"feedback.form.userNumber"})
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\AllowEmpty(true)
     * @Annotation\Validator({"name":"StringLength", "options":{"min":0, "max":50}})
     */
    public $userNumber;

    /**
     * $email
     *
     * @var String
     *
     * @Annotation\Name("email")
     * @Annotation\Options({"label":"feedback.form.email"})
     * @Annotation\Type("Zend\Form\Element\Email")
     * @Annotation\Validator({"name":"StringLength", "options":{"min":0, "max":60}})
     * @Annotation\Validator({"name":"EmailAddress"})
     * @Annotation\ErrorMessage("Email address is invalid")
     */
    public $email;

    // @codingStandardsIgnoreEnd
}
