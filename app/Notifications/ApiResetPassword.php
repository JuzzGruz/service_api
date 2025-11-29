<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Password;

class ApiResetPassword extends Notification
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
        // Токен для сброса пароля
        $token = Password::broker()->createToken($notifiable);
        $email = $notifiable->getEmailForPasswordReset();
        // Собираем ссылку Universal Link для приложения
        $universalLink = 'https://service.com/open-reset-password?email='
                        . urlencode($email)
                        . '&token=' . urlencode($token);

        return (new MailMessage)
            ->subject('Сброс пароля')
            ->line('Нажмите кнопку ниже, чтобы сбросить пароль.')
            ->action('Сбросить пароль', $universalLink)
            ->line('Если вы не запрашивали сброс пароля, игнорируйте это письмо.');
    }
}
