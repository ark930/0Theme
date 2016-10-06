<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LogEvent
{
    use InteractsWithSockets, SerializesModels;

    public $ip;
    public $user;
    public $type;
    public $message;
    public $time;

    const REGISTER = 'register';
    const REGISTER_CONFIRM = 'register_confirm';
    const LOGIN = 'login';
    const LOGOUT = 'logout';

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($ip, $user, $type, $message = null)
    {
        $this->ip = $ip;
        $this->user = $user;
        $this->type = $type;
        $this->message = $message;
        $this->time = date('Y-m-d H:i:s');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('log');
    }
}
