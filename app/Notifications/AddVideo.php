<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AddVideo extends Notification
{
    use Queueable;

    private $title;
    private $description;
    private $teacher_id;
    private $course_id;

    /**
     * Create a new notification instance.
     */
    public function __construct($course_id, $title, $description, $teacher_id)
    {
        $this->course_id = $course_id;
        $this->teacher_id = $teacher_id;
        $this->title = $title;
        $this->description = $description;
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
            'course_id' => $this->course_id,
            'title' => $this->title,
            'description' => $this->description,
            'teacher_id' => $this->teacher_id,
        ];
    }

}
