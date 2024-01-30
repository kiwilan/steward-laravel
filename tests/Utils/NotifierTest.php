<?php

namespace Kiwilan\Steward\Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Kiwilan\Steward\Utils\Notifier;

beforeEach(function () {
    Config::set('mail.mailer', dotenv()['MAIL_MAILER']);
    Config::set('mail.host', dotenv()['MAIL_HOST']);
    Config::set('mail.port', dotenv()['MAIL_PORT']);
    Config::set('mail.username', dotenv()['MAIL_USERNAME']);
    Config::set('mail.password', dotenv()['MAIL_PASSWORD']);
    Config::set('mail.encryption', dotenv()['MAIL_ENCRYPTION']);
    Config::set('mail.from.address', dotenv()['MAIL_FROM_ADDRESS']);
    Config::set('mail.from.name', dotenv()['MAIL_FROM_NAME']);
});

it('can use mail', function () {
    $notifier = Notifier::mail()
        ->to([dotenv()['MAIL_FROM_ADDRESS']])
        ->from(dotenv()['MAIL_FROM_ADDRESS'])
        ->mailer(dotenv()['MAIL_MAILER'])
        ->host(dotenv()['MAIL_HOST'])
        ->port(dotenv()['MAIL_PORT'])
        ->credentials(dotenv()['MAIL_USERNAME'], dotenv()['MAIL_PASSWORD'])
        ->encryption(dotenv()['MAIL_ENCRYPTION'])
        ->subject('Notify test')
        ->message('Notify test')
        ->send();

    expect($notifier)->toBeTrue();
});

it('can use mail auto', function () {
    $notifier = Notifier::mail()
        ->auto()
        ->to([dotenv()['MAIL_FROM_ADDRESS']])
        ->message('Notify test')
        ->send();

    expect($notifier)->toBeTrue();
});

it('can use slack', function () {
    $notifier = Notifier::slack(dotenv()['STEWARD_SLACK_WEBHOOK'])
        ->message('Notify test')
        ->send();

    expect($notifier)->toBeTrue();
});

it('can use discord', function () {
    $notifier = Notifier::discord(dotenv()['STEWARD_DISCORD_WEBHOOK'])
        ->username('Steward')
        ->avatarUrl('https://ewilan-riviere.com/images/ewilan-riviere.webp')
        ->message('Notify test')
        ->send();

    expect($notifier)->toBeTrue();
});

it('can use command', function () {
    $command = Artisan::call('notifier', [
        'message' => 'Notify test from command',
        '--type' => 'discord',
        '--webhook' => dotenv()['STEWARD_DISCORD_WEBHOOK'],
    ]);

    expect($command)->toBe(0);
});
