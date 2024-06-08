<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function getMessages()
    {
        $all_messages = [];
        $messages = $this->messages()->with('user')->get();
        foreach ($messages as $message) {
            $all_messages[] = [
                'fromUserId' => $message->user->id,
                'toUserId' => $this->users->where('id', '!=', $message->user->id)->first()->id,
                'text' => $message->content,
            ];
        }
        return $all_messages;
    }
}
