<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Storage;
use Vite;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Message $message)
    {
        //
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('conversation.'.$this->message->conversation_id);
    }

    public function broadcastWith(): array
    {
        $messageData = [
            'id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'user_id' => $this->message->user_id,
            'content' => $this->message->content,
            'type' => $this->message->type,
            'created_at' => $this->message->created_at->format('g:i A'),
            'user' => [
                'id' => $this->message->user->id,
                'name' => $this->message->user->name,
                'profile_picture' => $this->message->user->profile_picture
                    ? Storage::url($this->message->user->profile_picture)
                    : Vite::asset('resources/assets/images/default_profile.png'),
            ],
        ];

        // Handle metadata properly
        if ($this->message->metadata) {
            // Check if metadata is already an array or needs to be decoded
            if (is_string($this->message->metadata)) {
                $messageData['metadata'] = json_decode($this->message->metadata, true);
            } else {
                $messageData['metadata'] = $this->message->metadata;
            }
        } else {
            $messageData['metadata'] = null;
        }

        return [
            'message' => $messageData,
        ];
    }
}
