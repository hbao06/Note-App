<?php

namespace App\Events;

use App\Models\Note;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class NoteUpdated implements ShouldBroadcastNow
{
    use SerializesModels;

    public $note;
    public $userId;

    public function __construct(Note $note, $userId)
    {
        $this->note = $note;
        $this->userId = $userId;
    }

    public function broadcastOn()
    {
        return new Channel('note.' . $this->note->id);
    }

    public function broadcastAs()
    {
        return 'note.updated';
    }

    public function broadcastWith()
    {
        $user = User::find($this->userId);

        return [
            'id' => $this->note->id,
            'title' => $this->note->title,
            'content' => $this->note->content,
            'updated_by' => $this->userId,
            'updated_by_name' => $user?->name ?? 'Another user',
            'updated_by_email' => $user?->email ?? '',
            'updated_by_initial' => strtoupper(mb_substr($user?->name ?? 'U', 0, 1, 'UTF-8')),
            'updated_by_avatar' => $user && $user->avatar
                ? asset('storage/' . $user->avatar)
                : null,
            'updated_at' => optional($this->note->updated_at)->toISOString(),
        ];
    }
}