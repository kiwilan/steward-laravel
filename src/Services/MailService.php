<?php

namespace Kiwilan\Steward\Services;

use Kiwilan\Steward\Services\Mail\MailServer;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class MailService implements MailServer
{
    public function __construct(
        protected string $username = '',
        protected string $password = '',
        protected string $smtp = '',
        protected int $port = 0,
        protected int $result = 0,
        protected string $error = ''
    ) {
    }

    // TODO composer require symfony/mailgun-mailer symfony/http-client
    public static function create(string $username, string $password, string $smtp = 'smtp.mailtrap.io', int $port = 2525): MailService
    {
        return new MailService(
            $username,
            $password,
            $smtp,
            $port,
        );
    }

    /**
     * To test directly with a configured server,
     * `$server` have to be a `const` of `MailService`.
     */
    public static function testing(string $username, string $password, array $server, string $bodyHtml, string $email = '', string $bodyPlainText = 'text'): MailService
    {
        if (empty($email)) {
            $email = $username;
        }
        $service = self::create($username, $password, $server['smtp'], $server['port']);

        return $service->send('Testing', $bodyHtml, $bodyPlainText, $server['encryption'], [$email => 'User'], $email);
    }

    public function send(
        string $subject,
        string $bodyHtml,
        string $bodyPlainText = 'text',
        string $encryption = 'tls',
        array $from = ['no-reply@secob.fr' => 'Secob'],
        string $to = 'contact@mail.com'
    ): MailService {
        // try {
        //     // Create the SMTP transport
        //     $transport = (new Swift_SmtpTransport($this->smtp, $this->port, $encryption))
        //         ->setUsername($this->username)
        //         ->setPassword($this->password);

        //     $mailer = new Swift_Mailer($transport);

        //     // Create a message
        //     $message = new Swift_Message();

        //     $message->setSubject($subject);
        //     $message->setFrom($from);
        //     $message->addTo($to);

        //     // Set the plain-text part
        //     $message->setBody($bodyPlainText);
        //     // Set the HTML part
        //     $message->addPart($bodyHtml, 'text/html');
        //     // Send the message
        //     $this->result = $mailer->send($message);
        // } catch (\Throwable $th) {
        //     $this->error = $th->getMessage();
        // }

        return $this;
    }
}
