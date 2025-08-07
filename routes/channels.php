<?php

use App\Models\Chat;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    if (! $user) {
        return false;
    }

    $chat = Chat::find($chatId);
    if (! $chat) {
        return false;
    }

    return $chat->hasParticipant($user) ? [
        'id' => $user->id,
        'name' => $user->name,
        'initials' => $user->initials(),
    ] : false;
});
