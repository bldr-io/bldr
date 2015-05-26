<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Notify\Task;

use Bldr\Block\Core\Task\AbstractTask;
use Joli\JoliNotif\Notification;
use Joli\JoliNotif\NotifierFactory;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Aaron Scherer <aaron@undergroundelephant.com>
 */
class NotifyTask extends AbstractTask
{
    /**
     * @var array $smtp
     */
    private $smtp;

    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setName('notify')
            ->setDescription('Sends a notification to the screen, or to an email')
            ->addParameter('message', true, 'Message to show/send')
            ->addParameter('email', false, 'Email to send to')
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function run(OutputInterface $output)
    {
        $message = $this->getParameter('message');

        if (!$this->hasParameter('email')) {
            $this->sendDesktopNotification($message);

            return $output->writeln(["", '    <info>[notify]</info> - <comment>'.$message.'</comment>', ""]);
        }

        $this->sendEmail($output, $message);
    }

    /**
     * @param array $smtp
     */
    public function setSMTPInfo(array $smtp)
    {
        $this->smtp = $smtp;
    }

    /**
     * Sends an email with the given message
     *
     * @param OutputInterface $output
     * @param string          $content
     *
     * @return int
     */
    private function sendEmail(OutputInterface $output, $content)
    {
        $output->writeln("Sending an email");
        $transport = $this->getTransport();
        $mailer    = \Swift_Mailer::newInstance($transport);

        $message = \Swift_Message::newInstance('Bldr Notify - New Message')
            ->setFrom(['no-reply@bldr.io' => 'Bldr'])
            ->setTo($this->getEmails())
            ->setBody(
                "<p>Bldr has a new message for you from the most recent build</p>\n<br /><pre>{$content}</pre>\n",
                "text/html"
            )
            ->addPart($content, 'text/plain')
        ;

        $result = $mailer->send($message);

        return $result;
    }

    /**
     * @return mixed
     */
    private function getTransport()
    {
        if (null === $this->smtp) {
            return \Swift_MailTransport::newInstance();
        }

        return \Swift_SmtpTransport::newInstance($this->smtp['host'], $this->smtp['port'], $this->smtp['security'])
            ->setUsername($this->smtp['username'])
            ->setPassword($this->smtp['password'])
        ;
    }

    /**
     * @return array|int|string
     */
    private function getEmails()
    {
        if (strpos($this->getParameter('email'), ',') !== false) {
            return explode(',', $this->getParameter('email'));
        }

        return $this->getParameter('email');
    }

    /**
     * @param string $message
     */
    private function sendDesktopNotification($message)
    {
        $notifier = NotifierFactory::create();

        if ($notifier) {
            // Create your notification
            $notification =
                (new Notification())
                    ->setTitle('Bldr')
                    ->setBody($message)
                    ->setIcon(__DIR__.'/../Resources/image/notifier.png')
            ;

            // Send it
            $notifier->send($notification);
        }
    }
}
