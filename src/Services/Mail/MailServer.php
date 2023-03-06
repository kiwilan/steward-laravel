<?php

namespace Kiwilan\Steward\Services\Mail;

interface MailServer
{
    public const MAILTRAP = [
        'smtp' => 'smtp.mailtrap.io',
        'port' => 2525,
        'encryption' => 'tls',
    ];

    public const OUTLOOK = [
        'smtp' => 'smtp.office365.com',
        'port' => 587,
        'encryption' => 'tls',
    ];

    public const GMAIL = [
        'smtp' => 'smtp.gmail.com',
        'port' => 587,
        'encryption' => 'tls',
    ];
}
