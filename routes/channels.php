<?php

use App\Models\Chat;
use App\Models\Collaboration;
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

    if (! $chat->hasParticipant($user)) {
        return false;
    }

    return [
        'id' => $user->id,
        'name' => $user->name,
        'first_name' => $user->first_name,
        'initials' => $user->initials(),
        'avatar_url' => $user->avatar_url,
        'role' => $chat->getUserRole($user),
    ];
});

Broadcast::channel('collaboration.{collaborationId}', function ($user, $collaborationId) {
    if (! $user) {
        return false;
    }

    $collaboration = Collaboration::find($collaborationId);
    if (! $collaboration) {
        return false;
    }

    if (! $collaboration->hasParticipant($user)) {
        return false;
    }

    return [
        'id' => $user->id,
        'name' => $user->name,
        'role' => $collaboration->getUserRole($user),
    ];
});
