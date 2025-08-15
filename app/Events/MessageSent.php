<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public ChatMessage $message) 
    {
        \Log::info('MessageSent event created', [
            'message_id' => $this->message->id,
            'receiver_id' => $this->message->receiver_id
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): PrivateChannel
    {
        // Broadcast to the receiver's channel so they can receive the message
        $channel = new PrivateChannel('chat.' . $this->message->receiver_id);
        
        \Log::info('Broadcasting on channel', [
            'channel' => 'chat.' . $this->message->receiver_id
        ]);
        
        return $channel;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        // Load the sender relationship and return the complete message data
        $this->message->load('sender', 'receiver');
        
        $data = [
            'message' => $this->message->toArray()
        ];
        
        \Log::info('Broadcasting message data', $data);
        
        return $data;
    }
}