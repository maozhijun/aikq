<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ChatPushNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $nickname;
    public $message;
    public $time;
    public $mid;
    public $verification;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->nickname = $data['nickname'];
        $this->message = $data['message'];
        $this->time= $data['time'];
        $this->verification = $data['verification'];
        $this->mid = $data['mid'];
    }

    public function broadcastOn()
    {
        return ['akq_chat_notification'];
        // return new PrivateChannel('user.' . $this->body->id);
    }
}
