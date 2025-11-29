<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ApiVerifyEmail extends Notification
{
    use Queueable;

    /**
     * Каналы доставки уведомления
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Представление уведомления в виде письма
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = URL::temporarySignedRoute(
            'api.verification.verify', // имя API-роута
            now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
        
        $encodedUrl = urlencode($url);

        // Формируем Universal Link с оригинальной ссылкой в качестве параметра
        $universalLink = 'https://service.com/open-app?redirect=' . $encodedUrl;

        return (new MailMessage)
            ->subject('Подтвердите ваш email')
            ->line('Нажмите кнопку ниже, чтобы подтвердить email.')
            ->action('Подтвердить email', $universalLink)
            ->line('Базовая ссылка:')
            ->line($url)
            ->line('Если вы не создавали аккаунт, игнорируйте это письмо.');
    }
}
