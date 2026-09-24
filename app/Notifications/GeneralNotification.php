<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $message;
    protected $category;

    // Konstruktor menerima Judul, Pesan, dan Kategori opsional
    public function __construct($title, $message, $category = 'PROXIS')
    {
        $this->title = $title;
        $this->message = $message;
        $this->category = $category;
    }

    public function via($notifiable)
    {
        return ['database']; 
    }

    public function toArray($notifiable)
    {
        return [
            'name'     => $this->title,
            'message'  => $this->message,
            'category' => $this->category ?? 'PROXIS',
        ];
    }
}