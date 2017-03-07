<?php
/**
 * FeedbackController
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 10/12/15
 * Time: 11:16
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
 * @package  Controller
 * @author   Markus Mächler <markus.maechler@bithost.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\Controller;

use VuFind\Controller\FeedbackController as VuFindFeedbackController;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Mail as Mail;

/**
 * FeedbackController
 *
 * @category Swissbib_VuFind2
 * @package  Controller
 * @author   Markus Mächler <markus.maechler@bithost.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org
 */
class FeedbackController extends VuFindFeedbackController
{
    /**
     * Display Feedback home form.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function homeAction()
    {
        return $this->forwardTo('Feedback', 'Email');
    }

    /**
     * Receives input from the user and sends an email to the recipient set in
     * the config.ini
     *
     * @return void
     */
    public function emailAction()
    {
        /**
         * FeedbackForm
         *
         * @var Form $feedbackForm
         */
        $feedbackForm = $this->serviceLocator
            ->get('Swissbib\Feedback\Form\FeedbackForm');

        if ($this->request->isPost()
            && $this->request->getPost('form-name') === 'swissbibfeedback'
        ) {
            $feedbackForm->setData($this->request->getPost());

            if ($feedbackForm->isValid()) {
                $this->sendMail($feedbackForm->getData());
                $this->resetForm($feedbackForm);

                $this->flashMessenger()
                    ->addMessage('feedback.form.success', 'success');
            } else {
                $this->flashMessenger()
                    ->addMessage('feedback.form.error', 'error');
            }
        }

        $feedbackForm->setAttribute('action', '');
        $feedbackForm->setAttribute('method', 'post');
        $feedbackForm->setAttribute('class', 'form-horizontal');
        $feedbackForm->prepare();

        return $this->createViewModel(
            [
                'form' => $feedbackForm
            ]
        );
    }

    /**
     * Resetting the values of the form passed. Unfortunately there is  no other way
     * in ZF2 to achieve this.
     *
     * @param Form $form Zend form to be reset
     *
     * @return void
     */
    protected function resetForm(Form $form)
    {
        $resetTypes = ['text', 'radio', 'email', 'textarea'];

        /**
         * Form element
         *
         * @var Element $element
         */
        foreach ($form->getElements() as $element) {
            if (in_array($element->getAttribute('type'), $resetTypes)) {
                $element->setValue('');
            }
        }
    }

    /**
     * Sending mail to admin
     *
     * @param array $data User / Mail information
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function sendMail(array $data)
    {
        $config = $this->getServiceLocator()->get('VuFind\Config')->get('config');

        // These settings are set in the feedback settion of your config.ini
        $feedback = isset($config->Feedback) ? $config->Feedback : null;
        $recipientEmail = isset($feedback->recipient_email)
            ? $feedback->recipient_email : null;
        $recipientName = isset($feedback->recipient_name)
            ? $feedback->recipient_name : 'Your Library';

        if ($recipientEmail == null) {
            throw new \Exception(
                'Feedback Module Error: Recipient Email Unset (see config.ini)'
            );
        }

        $emailMessage = 'Name: ' . $data['name'] . "\n";
        $emailMessage .= 'Benutzernummer: ' . $data['userNumber'] . "\n";
        $emailMessage .= 'Email: ' . $data['email'] . "\n";
        $emailMessage .= 'Frage / Kommentar: ' . $data['question'] . "\n";

        // This sets up the email to be sent
        $mail = new Mail\Message();
        $mail->setEncoding('UTF-8');
        $mail->setBody($emailMessage);
        $mail->setFrom($data['email'], $data['name']);
        $mail->addTo($recipientEmail, $recipientName);
        $mail->setSubject($this->translate($data['questionType']));

        $this->getServiceLocator()->get('VuFind\Mailer')->getTransport()
            ->send($mail);
    }
}
