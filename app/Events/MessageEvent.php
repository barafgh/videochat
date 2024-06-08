<?php

namespace App\Events;

use App\Models\Message;
use App\Models\Room;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $message;
    public int $roomId;
    public int $from_id;
    public int $to_id;

    public function __construct(string $message, int $roomId)
    {
        $room = Room::find($roomId);
        if (!$room) {
            throw new \Exception("Room not found");
        }

        $this->message = $message;
        $this->from_id = auth()->id();
        $this->roomId = $roomId;
        $this->to_id = $room->users->where('id', '!=', auth()->id())->first()->id ?? null;

    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('messages.rooms.' . $this->roomId)
        ];
    }
}
