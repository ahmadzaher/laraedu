<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Feedback extends Notification
{
    use Queueable;


    /**
     * The feedback text.
     *
     * @var string
     */
    public $text;
    public $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($text)
    {
        $this->text = $text;
        $this->user = auth()->user();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('رأي مستخدم جديد')
            ->greeting('مرحبا!')
            ->salutation('تحياتي, <br>'.option('app_name'))
            ->line('أنت تتلقى هذا البريد الإلكتروني لأننا تلقينا رأي لمستخدم جديد') // Here are the lines you can safely override
            ->line("<strong>$this->text</strong>")
            ->line("اسم المستخدم: " . $this->user->name);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
