<?php

namespace Kiwilan\Steward\Utils\Notifier;

use Illuminate\Support\Facades\Log;
use Kiwilan\Steward\Utils\Notifier;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class NotifierMail
{
    /**
     * @param  Address[]  $to  Array of `Address` object
     */
    protected function __construct(
        protected Notifier $notifier,
        protected string $mailer = 'smtp',
        protected string $host = 'mailpit',
        protected int $port = 1025,
        protected string $encryption = 'tls',
        protected ?string $username = null,
        protected ?string $password = null,
        protected ?TransportInterface $stransport = null,
        protected ?Email $semail = null,
        protected ?Mailer $smailer = null,
        protected array $to = [],
        protected ?Address $from = null,
        protected ?Address $replyTo = null,
        protected ?string $subject = null,
        protected ?string $message = null,
        protected ?string $html = null,
    ) {
    }

    public static function make(Notifier $notifier): self
    {
        return new self($notifier);
    }

    /**
     * @param  string  $mailer  Mailer transport, default `smtp`
     */
    public function mailer(string $mailer): self
    {
        $this->mailer = $mailer;

        return $this;
    }

    /**
     * @param  string  $host  Mailer host, default `mailpit`
     */
    public function host(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @param  int  $port  Mailer port, default `1025`
     */
    public function port(int $port): self
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @param  string  $encryption  Mailer encryption, default `tls`
     */
    public function encryption(string $encryption): self
    {
        $this->encryption = $encryption;

        return $this;
    }

    public function credentials(string $username, string $password): self
    {
        $this->username = $username;
        $this->password = $password;

        return $this;
    }

    /**
     * Use default mailer from `.env` file.
     */
    public function auto(): self
    {
        $this->mailer = config('mail.mailer');
        $this->host = config('mail.host');
        $this->port = config('mail.port');
        $this->encryption = config('mail.encryption');
        $this->username = config('mail.username');
        $this->password = config('mail.password');
        $this->from = config('mail.from.address');

        return $this;
    }

    /**
     * @param  Address[]|string  $to  Array of `Address` object
     * @param  string|null  $name  Useful if `$to` is a string
     */
    public function to(array|string $to, ?string $name = null): self
    {
        if (is_string($to)) {
            $to = [new Address($to, $name)];
        }

        $this->to = $to;

        return $this;
    }

    public function from(string $from, ?string $name = null): self
    {
        $this->from = new Address($from, $name);

        return $this;
    }

    public function replyTo(string $replyTo, ?string $name = null): self
    {
        $this->replyTo = new Address($replyTo, $name);

        return $this;
    }

    public function subject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function message(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function html(string $html): self
    {
        $this->html = $html;

        return $this;
    }

    public function send(): void
    {
        $this->stransport = Transport::fromDsn("{$this->mailer}://{$this->host}:{$this->port}");
        $this->smailer = new Mailer($this->stransport);

        $this->semail = (new Email())
            ->to(...$this->to)
            ->from($this->from);

        if ($this->replyTo) {
            $this->semail->replyTo($this->replyTo);
        }

        if ($this->subject) {
            $this->semail->subject($this->subject);
        }

        if ($this->message) {
            $this->semail->text($this->message);
        }

        if ($this->html) {
            $this->semail->html($this->html);
        }

        try {
            $this->smailer->send($this->semail);
        } catch (\Throwable $th) {
            Log::error("Mailer notification failed: {$th->getMessage()}", $this->toArray());
        }

        Log::debug('Mailer notification sent', $this->toArray());
    }

    public function toArray(): array
    {
        return [
            'mailer' => $this->mailer,
            'host' => $this->host,
            'port' => $this->port,
            'encryption' => $this->encryption,
            'username' => $this->username,
            'password' => $this->password,
            'to' => $this->to,
            'from' => $this->from,
            'replyTo' => $this->replyTo,
            'subject' => $this->subject,
            'message' => $this->message,
            'html' => $this->html,
        ];
    }
}
