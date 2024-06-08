<?php

use App\Models\Room;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('messages.rooms.{id}', function ($user, $id) {
    // Check if the user is authenticated
    if (!$user) {
        return false;
    }

    // Check if the room exists
    $room = Room::find($id);
    if (!$room) {
        return false;
    }

    // Check if the user is part of the room
    return $room->users->contains($user->id);
});

Broadcast::channel('call.{id}', function ($user, $id) {
    // Check if the user is authenticated
    if (!$user) {
        return false;
    }

    // Check if the user id matches the authenticated user id
    return (int) $user->id === (int) $id;
});
