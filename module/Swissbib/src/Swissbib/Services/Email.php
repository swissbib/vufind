<?php
/**
 * Service for sending e-mail.
 *
 * PHP version 7
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category Swissbib_VuFind
 * @package  Service
 * @author   Simone Cogno <scogno@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace Swissbib\Services;

use Swissbib\VuFind\Db\Row\PuraUser;
use Laminas\Mail\Message;
use Laminas\Mail\Transport\Sendmail as SendmailTransport;
use Laminas\Mail\Transport\Smtp as SmtpTransport;
use Laminas\Mime;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class Email.
 *
 * @category Swissbib_VuFind
 * @package  Service
 * @author   Simone Cogno <scogno@snowflake.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Email
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
     * @param array                   $config         Config.
     * @param ServiceLocatorInterface $serviceLocator Service locator
     */
    public function __construct($config, $serviceLocator)
    {
        $this->config = $config;
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Send e-mail with attachment.
     *
     * @param string $to                 The recipient of the e-mail
     * @param string $subject            Subject of the e-mail
     * @param string $textMail           Text of the e-mail
     * @param string $attachmentFilePath File path of the file to attach
     * @param bool   $tls                tls
     *
     * @return void
     * @throws \Exception
     */
    public function sendMail(
        $to,
        $subject,
        $textMail,
        $attachmentFilePath = "",
        $tls = false
    ) {
        $mimeMessage = $this->createMimeMessage($textMail, $attachmentFilePath);
        $this->sendMailWithAttachment(
            $to,
            $mimeMessage,
            $subject,
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
        $text->setEncoding(Mime\Mime::ENCODING_QUOTEDPRINTABLE);
        $text->charset = 'UTF-8';
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
        $emailAddressFrom = $this->config->get('config')['Site']['email'];
        $message = new Message();
        $message->setBody($mimeMessage);
        $message->addTo($to)
            ->addFrom($emailAddressFrom)
            ->setSubject($subject);
        $transport = null;
        if ($tlsActive) {
            $transport = new SmtpTransport();
        } else {
            $transport = new SendmailTransport();
        }
        $transport->send($message);
    }

    /**
     * Send the National Licence account extension e-mail to a specific user.
     *
     * @param string $toUser User e-mail that the e-mail will be sent to.
     *
     * @return void
     * @throws \Exception
     */
    public function sendAccountExtensionEmail($toUser)
    {
        $sl = $this->getServiceLocator();
        $vhm = $sl->get('ViewHelperManager');
        $url = $vhm->get('url');
        $baseDomainPath = $this->config->get('config')['Site']['url'];
        $link =  $baseDomainPath .
            $url(
                'national-licences',
                ['action' => 'index']
            );
        $username = $toUser->firstname . ' ' . $toUser->lastname;

        $textMailDe = '<p>Liebe(r) ' . $username .
            ',<br /> <br /> Seit einem Jahr, haben Sie ' .
            'Schweizer Nationallizenzen nicht mehr benutzt. ' .
            'Wir haben Ihr Konto deshalb deaktiviert. ' .
            'Wenn Sie wollen, können Sie Ihres Konto ' .
            '<a href="' . $link . '" ' .
            'target="_blank" rel="noreferrer">reaktivieren</a>' .
            '.</p> ' .
            '<p>Schweizer Nationallizenzen<br />' .
            '<a href="http://nationallizenzen.ch">' .
            'http://nationallizenzen.ch</a></p>';

        $textMailFr = '<p>Cher/Chère ' . $username .
            ',<br /> <br /> Vous n\'avez pas utilisé les ' .
            'Licences Nationales Suisses dans les 12 derniers mois. ' .
            'Nous avons donc désactivé votre compte. ' .
            'Néanmoins, vous pouvez le réactiver' .
            ' en visitant <a href="' . $link . '" ' .
            'target="_blank" rel="noreferrer">ce lien</a>' .
            '.</p> ' .
            '<p>Licences Nationales Suisses<br />' .
            '<a href="http://licencesnationales.ch">' .
            'http://licencesnationales.ch</a></p>';

        $textMailEn = '<p>Dear ' . $username .
            ',<br /> <br /> We noticed that you didn\'t use ' .
            'Swiss National Licences as a private user ' .
            'in the last 12 months. ' .
            'Therefore we deactivated your account. ' .
            'Please visit <a href="' . $link . '" ' .
            'target="_blank" rel="noreferrer">this link</a> ' .
            'if you wish to reactivate your account.</p> ' .
            '<p>Swiss National Licences<br />' .
            '<a href="http://nationallicences.ch">' .
            'http://nationallicences.ch</a></p>';

        $textMail = $toUser->email . "<br />" .
            $textMailDe . '<p>---</p>' .
            $textMailFr . '<p>---</p>' .
            $textMailEn;

        $mimeMessage = $this->createMimeMessage(
            $textMail,
            null,
            Mime\Mime::TYPE_HTML
        );
        $this->sendMailWithAttachment(
            //$toUser->email,
            'lionel.walter@unibas.ch',
            $mimeMessage,
            'Nationallizenzen / Licences Nationales / National licences',
            //use 'true' to test locally if sendmail not installed
            'true'
        );
    }

    /**
     * Send the Pura account extension e-mail to a specific user.
     *
     * @param puraUser $puraUser        Pura User (pura sepcific infos)
     * @param array    $vufindUser      Vufind User (name, email, ...)
     * @param array    $institutionInfo Infos about related library
     *
     * @return void
     * @throws \Exception
     */
    public function sendPuraAccountExtensionEmail(
        puraUser $puraUser, $vufindUser, array $institutionInfo
    ) {
        $sl = $this->getServiceLocator();
        $vhm = $sl->get('ViewHelperManager');
        $url = $vhm->get('url');
        $baseDomainPath = $this->config->get('config')['Site']['url'];
        $link =  $baseDomainPath .
            $url(
                'pura/library',
                [
                    'libraryCode' => $puraUser->getLibraryCode(),
                    'page' => 'registration',
                ]
            );
        $username = $vufindUser->firstname . ' ' . $vufindUser->lastname;

        $textMailDe = '<p>Guten Tag Frau/Herr ' . $username .
            ',<br /> <br />Ihre Einschreibung für die Dienstleistung PURA (' .
            $institutionInfo['name']['de'] .
            ') läuft am ' .
            $puraUser->getExpirationDate()->format('j.n.Y') .
            ' ab. Falls Sie den Service auch weiterhin nutzen möchten, ' .
            'bitten wir Sie, ' .
            '<a href="' . $link . '" ' .
            'target="_blank" rel="noreferrer">Ihre Einschreibung</a> nun zu ' .
            'erneuern indem Sie sich in der Bibliothek (' .
            $institutionInfo['name']['de'] .
            ') freischalten lassen. ' .
            'Andernfalls wird Ihr Konto demnächst deaktiviert. ' .
            'Eine spätere Reaktivierung des Kontos bleibt allerdings möglich.' .
            '</p>' .
            '<p>Freundliche Grüsse,</p>' .
            '<p>swissbib Team<br />' .
            '<a href="https://www.swissbib.ch">' .
            'https://www.swissbib.ch</a></p>';

        $textMailEn = '<p>Dear ' . $username .
            ',<br /> <br />Your registration for PURA (' .
            $institutionInfo['name']['en'] .
            ') expires on ' .
            $puraUser->getExpirationDate()->format('j.n.Y') .
            '. If you wish to continue using the service, please ' .
            'renew your <a href="' . $link . '" ' .
            'target="_blank" rel="noreferrer">registration</a> personally ' .
            'at the counter (' .
            $institutionInfo['name']['en'] .
            '). ' .
            'Otherwise your account will be deactivated soon. ' .
            'You will, however, be able to reactivate your account ' .
            'at a later stage.' .
            '</p>' .
            '<p>Best regards,</p>' .
            '<p>Team swissbib<br />' .
            '<a href="https://www.swissbib.ch">' .
            'https://www.swissbib.ch</a></p>';

        $textMailFr = '<p>Cher/Chère ' . $username .
            ',<br /> <br />Votre inscription pour le service PURA (' .
            $institutionInfo['name']['fr'] .
            ') arrivera à échéance le ' .
            $puraUser->getExpirationDate()->format('j.n.Y') .
            '. Si vous le désirez, vous pouvez renouveler <a href="' . $link . '" ' .
            'target="_blank" rel="noreferrer">votre inscription</a>, ' .
            'en vous présentant personnellement au guichet de la bibliothèque (' .
            $institutionInfo['name']['fr'] .
            '). ' .
            'Sinon, votre compte sera désactivé prochainement. Une réactivation ' .
            'ultérieure demeure possible.' .
            '</p>' .
            '<p>Avec nos meilleures salutations,</p>' .
            '<p>Votre service swissbib<br />' .
            '<a href="https://www.swissbib.ch">' .
            'https://www.swissbib.ch</a></p>';

        $textMail =  $textMailDe . '<p>---</p>' .
            $textMailEn . '<p>---</p>' .
            $textMailFr;

        $mimeMessage = $this->createMimeMessage(
            $textMail,
            null,
            Mime\Mime::TYPE_HTML
        );
        $this->sendMailWithAttachment(
            $vufindUser->email,
            //'lionel.walter@unibas.ch',
            $mimeMessage,
            'Ihr PURA-Login läuft demnächst ab / Your PURA account expires soon',
            //use 'true' to test locally if sendmail not installed
            'false'
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
