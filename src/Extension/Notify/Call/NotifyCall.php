<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Extension\Notify\Call;

use Bldr\Call\AbstractCall;

/**
 * @author Aaron Scherer <aaron@undergroundelephant.com>
 */
class NotifyCall extends AbstractCall
{
    /**
     * @var array $smtp
     */
    private $smtp;

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        if (!$this->getCall()->has('message')) {
            throw new \Exception("Notify must have a message.");
        }

        $message = $this->getCall()->message;

        $formatter = $this->getHelperSet()->get('formatter');
        if (!$this->getCall()->has('email')) {
            return $this->getOutput()->writeln($formatter->formatSection('notify', $message));
        }

        return $this->sendEmail($message);
    }

    /**
     * Sends an email with the given message
     */
    private function sendEmail($content)
    {
        $this->getOutput()->writeln("Sending an email");
        $transport = $this->getTransport();
        $mailer    = \Swift_Mailer::newInstance($transport);

        $message = \Swift_Message::newInstance('Bldr Notify - New Message')
            ->setFrom(['no-reply@bldr.io' => 'Bldr'])
            ->setTo(strpos($this->getCall()->email, ',') !== false ? explode(',', $this->getCall()->email) : $this->getCall()->email)
            ->setBody("<p>Bldr has a new message for you from the most recent build</p>\n<br /><pre>{$content}</pre>\n", "text/html")
            ->addPart($content, 'text/plain');

        $result = $mailer->send($message);

        return $result;
    }

    /**
     *
     */
    private function getTransport()
    {
        if (null === $this->smtp) {
            return \Swift_MailTransport::newInstance();
        }

        return \Swift_SmtpTransport::newInstance($this->smtp['host'], $this->smtp['port'], $this->smtp['security'])
            ->setUsername($this->smtp['username'])
            ->setPassword($this->smtp['password']);
    }

    public function setSMTPInfo(array $smtp)
    {
        $this->smtp = $smtp;
    }
}
