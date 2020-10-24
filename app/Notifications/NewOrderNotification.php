<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Messages\NexmoMessage;

class NewOrderNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // mail, database, broadcast, slack, nexmo
        return [
            'mail', 'database', 'broadcast', 'nexmo'
        ];
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
                    //->from('info@local', 'Store')
                    ->subject('New Order')
                    ->greeting("Hello $notifiable->name,")
                    ->line("A new order #%s created")
                    ->action('View Order', url('/'))
                    ->line('Thank you for using our application!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "A new order #%s created.",
            'action' => url('/'),
            'icon' => '',
        ];
    }

    public function toBroadcast($notifiable)
    {
        return [
            'message' => "A new order #%s created.",
            'action' => url('/'),
            'icon' => '',
            'time' => now()->diffForHumans(),
            'user' => Auth::user()->name,
        ];
    }

    public function toNexmo($notifiable)
    {
        $message = new NexmoMessage();
        $message->content("A new order #%s created.");
        return $message;
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
