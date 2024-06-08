<?php
namespace App\Livewire\Pages;

use App\Events\CallEvent;
use App\Events\MessageEvent;
use App\Models\Message;
use App\Models\Room;
use App\Models\User;
use App\Models\CallSummary;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ChatComponent extends Component
{
    public array $messages = [];
    public string $message = '';
    public array $authUser = [];
    public array $contacts = [];
    public int $currentRoomId;
    public array $selectedUser = [];

    public function getListeners()
    {
        $rooms = Room::query()->whereHas('users', function ($query) {
            $query->where('user_id', Auth::id());
        })->pluck('id')->toArray();

        $listeners = [];
        foreach ($rooms as $roomId) {
            $listeners["echo-private:messages.rooms.{$roomId},MessageEvent"] = 'onMessageSent';
        }
        $listeners["echo-private:call.".Auth::id().",CallEvent"] = 'onCallReceived';

        return $listeners;
    }

    public function mount()
    {
        $user = Auth::user();
        $this->authUser = [
            'id' => $user->id,
            'name' => $user->name,
            'path' => $user->picture,
            'designation' => 'Software Engineer'
        ];
        $this->getContacts();
    }

    public function sendMessage($to_user_id)
    {
        $room = Room::query()->whereHas('users', function ($query) use ($to_user_id) {
            $query->where('user_id', Auth::id());
        })->whereHas('users', function ($query) use ($to_user_id) {
            $query->where('user_id', $to_user_id);
        })->first();

        if ($room) {
            $message = Message::create([
                'room_id' => $room->id,
                'user_id' => Auth::id(),
                'content' => $this->message,
            ]);
            MessageEvent::dispatch($message->content, $room->id);
            $this->messages[] = [
                'fromUserId' => Auth::id(),
                'toUserId' => $to_user_id,
                'text' => $this->message,
                'time' => 'Just now',
            ];
            $this->reset('message');
        }
    }

    public function loadMessages($roomId)
    {
        $room = Room::find($roomId);
        $this->messages = $room->messages->map(function ($message) {
            return [
                'fromUserId' => $message->user_id,
                'toUserId' => Auth::id(),
                'text' => $message->content,
                'time' => $message->created_at->diffForHumans(),
            ];
        })->toArray();
    }

    public function onMessageSent($event)
    {
        $roomId = $event['roomId'];
        $fromUserId = $event['from_id'];
        $toUserId = $event['to_id'];
        $message = $event['message'];
        $this->messages[] = [
            'fromUserId' => $fromUserId,
            'toUserId' => $toUserId,
            'text' => $message,
            'time' => 'Just now',
        ];
        $this->dispatch('refreshContacts', $this->contacts);
    }

    public function onCallReceived($event)
    {
        $this->dispatch('call-received', $event);
    }

    public function saveCallSummary($summary, $user_id)
    {
        CallSummary::create([
            'caller_id' => Auth::id(),
            'recipient_id' => $user_id,
            'duration' => $summary['duration'],
            'status' => $summary['status'],
        ]);
    }

    public function render()
    {
        return view('livewire.pages.chat-component');
    }

    private function getContacts()
    {
        // Create user rooms
        $allUsers = User::query()->where('id', '!=', Auth::id())->pluck('id')->toArray();
        foreach ($allUsers as $userId) {
            $room = Room::query()->whereHas('users', function ($query) use ($userId) {
                $query->where('user_id', Auth::id());
            })->whereHas('users', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->first();

            if (!$room) {
                $room = Room::create();
                $room->users()->attach([Auth::id(), $userId]);
            }
        }

        $rooms = Room::query()->whereHas('users', function ($query) {
            $query->where('user_id', Auth::id());
        })->get();

        foreach ($rooms as $room) {
            $user = $room->users->where('id', '!=', Auth::id())->first();
            $this->contacts[] = [
                'userId' => $user->id,
                'roomId' => $room->id,
                'name' => $user->name,
                'path' => $user->picture,
                'time' => $room->messages->last() ? $room->messages->last()->created_at->diffForHumans() : '',
                'preview' => $room->messages->last() ? $room->messages->last()->content : 'start a new chat',
                'active' => true,
            ];
        }
    }

    public function sendSignal($to_user_id, $data)
    {
        CallEvent::dispatch([
            'to_user_id' => $to_user_id,
            'from_user_id' => Auth::id(),
            'signal' => $data,
            'caller' => [
                'id' => Auth::id(),
                'name' => Auth::user()->name,
                'path' => Auth::user()->picture,
            ],
        ]);
    }

}
