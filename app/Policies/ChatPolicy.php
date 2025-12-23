<?php

namespace App\Policies;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChatPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any chats.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the chat.
     */
    public function view(User $user, Chat $chat): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $chat->hasParticipant($user);
    }

    /**
     * Determine whether the user can send messages to the chat.
     */
    public function sendMessage(User $user, Chat $chat): bool
    {
        if ($user->isAdmin()) {
            return $chat->isActive();
        }

        return $chat->canSendMessage($user);
    }

    /**
     * Determine whether the user can react to messages in the chat.
     */
    public function react(User $user, Chat $chat): bool
    {
        return $this->view($user, $chat);
    }

    /**
     * Determine whether the user can archive the chat.
     */
    public function archive(User $user, Chat $chat): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Only business members can archive chats
        return $chat->isBusinessMember($user);
    }
}
