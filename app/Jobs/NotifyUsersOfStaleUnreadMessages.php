<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
// 👇 use Query\Builder for whereExists closures
use Illuminate\Support\Facades\DB;

class NotifyUsersOfStaleUnreadMessages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $olderThan = Carbon::now()->subDay();

        User::query()
            ->whereExists(function (QueryBuilder $q) use ($olderThan) {
                $q->selectRaw('1')
                    ->from('messages as m')
                  // user participates in this chat (infer from any message they've sent in it)
                    ->whereExists(function (QueryBuilder $qq) {
                        $qq->selectRaw('1')
                            ->from('messages as mine')
                            ->whereColumn('mine.chat_id', 'm.chat_id')
                            ->whereColumn('mine.user_id', 'users.id')
                            ->limit(1);
                    })
                    ->whereColumn('m.user_id', '!=', 'users.id')      // sent by someone else
                    ->where('m.created_at', '<=', $olderThan)         // older than 24h
                    ->whereNull('m.read_at')                          // still unread (your schema)
                    ->where(function (QueryBuilder $n) {
                        $n->whereNull('users.unread_notified_up_to')
                            ->orWhereColumn('m.created_at', '>', 'users.unread_notified_up_to');
                    })
                    ->limit(1);
            })
            ->chunkById(500, function ($users) use ($olderThan) {
                foreach ($users as $user) {
                    DB::transaction(function () use ($user, $olderThan) {
                        $maxCreatedAt = Message::query()
                            ->from('messages as m')
                            ->whereExists(function (QueryBuilder $qq) use ($user) {
                                $qq->selectRaw('1')
                                    ->from('messages as mine')
                                    ->whereColumn('mine.chat_id', 'm.chat_id')
                                    ->where('mine.user_id', $user->id)
                                    ->limit(1);
                            })
                            ->where('m.user_id', '!=', $user->id)
                            ->whereNull('m.read_at')
                            ->where('m.created_at', '<=', $olderThan)
                            ->when($user->unread_notified_up_to, function ($q) use ($user) {
                                $q->where('m.created_at', '>', $user->unread_notified_up_to);
                            })
                            ->max('m.created_at');

                        if (! $maxCreatedAt) {
                            return;
                        }

                        $user->notify(new \App\Notifications\UnreadMessagesReminderNotification);

                        $user->forceFill([
                            'unread_notified_up_to' => $maxCreatedAt,
                        ])->save();
                    });
                }
            });
    }
}
