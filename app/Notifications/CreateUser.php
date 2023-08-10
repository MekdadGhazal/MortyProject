<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CreateUser extends Notification
{
    use Queueable;

    private $name;
    private $eamil;
    private $user_id;
    /**
     * Create a new notification instance.
     */
    public function __construct($user_id, $name, $eamil)
    {
        $this->user_id = $user_id;
        $this->name = $name;
        $this->eamil = $eamil;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
//    public function toMail(object $notifiable): MailMessage
//    {
//        return (new MailMessage)
//                    ->line('The introduction to the notification.')
//                    ->action('Notification Action', url('/'))
//                    ->line('Thank you for using our application!');
//    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'id' =>$this->user_id,
            'name' => $this->name,
            'email' => $this->eamil,
            'by'=> '',
        ];
    }
}
