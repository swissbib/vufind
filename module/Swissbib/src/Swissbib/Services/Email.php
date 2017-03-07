<?php
/**
 * Service for sending e-mail.
 *
 * PHP version 5
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category Swissbib_VuFind2
 * @package  Service
 * @author   Simone Cogno <scogno@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\Services;

use Zend\Di\ServiceLocator;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mime;
use Zend\ServiceManager\ServiceManager;
//use Zend\Mail\Transport\Sendmail as SendmailTransport;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Transport\Sendmail as SendmailTransport;

/**
 * Class Email.
 *
 * @category Swissbib_VuFind2
 * @package  Service
 * @author   Simone Cogno <scogno@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Email implements ServiceLocatorAwareInterface
{
    /**
     * Service locator.
     *
     * @var ServiceLocatorInterface $serviceLocator ServiceLocatorInterface.
     */
    protected $serviceLocator;
    /**
     * Config.
     *
     * @var array $config
     */
    protected $config;

    /**
     * Email constructor.
     *
     * @param array $config Config.
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Send e-mail with attachment.
     *
     * @param string $to                 The recipient of the e-mail
     * @param string $textMail           Text of the e-mail
     * @param string $attachmentFilePath File path of the file to attach
     * @param bool   $tls                tls
     *
     * @return void
     * @throws \Exception
     */
    public function sendMail($to, $textMail, $attachmentFilePath, $tls = false)
    {
        $mimeMessage = $this->createMimeMessage($textMail, $attachmentFilePath);
        $this->sendMailWithAttachment(
            $to,
            $mimeMessage,
            'National licence user export',
            $tls
        );
    }

    /**
     * Create mime message with email text and attached file.
     *
     * @param string $textMail           Email text
     * @param string $attachmentFilePath Attachment file path
     * @param int    $contentType        Content type
     *
     * @return Mime\Message
     */
    public function createMimeMessage(
        $textMail,
        $attachmentFilePath = null,
        $contentType = null
    ) {
        if (empty($contentType)) {
            $contentType = Mime\Mime::TYPE_HTML;
        }

        $mimeMessage = new Mime\Message();

        // first create the parts
        $text = new Mime\Part();
        $text->type = $contentType;
        $text->charset = 'utf-8';
        $text->setContent($textMail);

        if (!empty($attachmentFilePath)) {
            //Get the attached file reference
            $fileContent = fopen($attachmentFilePath, 'r')
            or die('Unable to open file!');
            $attachment = new Mime\Part($fileContent);
            $attachment->type = 'text/csv';
            $attachment->filename = 'user_export.csv';
            $attachment->disposition = Mime\Mime::DISPOSITION_ATTACHMENT;
            // Setting the encoding is recommended for binary data
            $attachment->encoding = Mime\Mime::ENCODING_BASE64;
            // then add them to a MIME message
            $mimeMessage->setParts([$text, $attachment]);
        } else {
            // then add it to MIME message
            $mimeMessage->setParts([$text]);
        }

        return $mimeMessage;
    }

    /**
     * Send e-mail with defined mime message (text and attached file).
     *
     * @param string       $to          Recipient.
     * @param Mime\Message $mimeMessage Mime message
     * @param string       $subject     Subject
     * @param bool         $tlsActive   Send with TLS encryption
     *
     * @return void
     * @throws \Exception
     */
    public function sendMailWithAttachment(
        $to, $mimeMessage, $subject, $tlsActive = false
    ) {
    
        if (empty($to)) {
            throw new \Exception(
                'Impossible to send the e-mail: recipient not given'
            );
        }
        // and finally we create the actual email
        $emailAddressFrom = $this->config
            ->get('NationalLicences')['EmailService']['default_email_address_from'];
        $message = new Message();
        $message->setBody($mimeMessage);
        $message->addTo($to)
            ->addFrom($emailAddressFrom)
            ->setSubject($subject);
        $transport = null;
        if ($tlsActive) {
            $transport = new SmtpTransport();
            $options
                = new SmtpOptions(
                    $this->config
                        ->get('NationalLicences')['EmailService']['SmtpOptions']
                );
            $transport->setOptions($options);
        } else {
            $transport = new SendmailTransport();
        }
        $transport->send($message);
    }

    /**
     * Send the account extension e-mail to a specific user.
     *
     * @param string $toUser User e-mail that the e-mail will be sent to.
     *
     * @return void
     * @throws \Exception
     */
    public function sendAccountExtensionEmail($toUser)
    {
        $sl = $this->getServiceLocator();
        $vhm = $sl->get('viewhelpermanager');
        $url = $vhm->get('url');
        $baseDomainPath = $this->config->get('config')['Site']['url'];
        $link =  $baseDomainPath .
            $url(
                'national-licences',
                ['action' => 'extend-account'],
                ['force_canonical' => true]
            );
        $username = $toUser->firstname . ' ' . $toUser->lastname;
        $textMail = '<p>Dear ' . $username .
            ',<br /> <br /> We noticed that you didn\'t use ' .
            'Swiss National Licences as a private user in the last 12 months. ' .
            'Please visit <a href="' . $link . '" ' .
            'target="_blank" rel="noreferrer">this link</a> ' .
            'in the next 30 days to keep your account active. ' .
            'Take this occasion to update ' .
            'your personal information if needed. ' .
            'Otherwise your account will be made inactive' .
            ' and you will need to register again.</p>';
        $mimeMessage = $this->createMimeMessage(
            $textMail,
            null,
            Mime\Mime::TYPE_HTML
        );
        $this->sendMailWithAttachment(
            $toUser->email, $mimeMessage, 'Account extension'
        );
    }

    /**
     * Retrieve serviceManager instance.
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Set serviceManager instance.
     *
     * @param ServiceLocatorInterface $serviceLocator ServiceLocatorInterface
     *
     * @return void
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
}
