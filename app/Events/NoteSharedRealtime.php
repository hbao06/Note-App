<?php

namespace App\Events;

use App\Models\Note;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class NoteSharedRealtime implements ShouldBroadcastNow
{
    use SerializesModels;

    public function __construct(
        public Note $note,
        public User $recipient,
        public array $notificationData
    ) {}

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->recipient->id);
    }

    public function broadcastAs()
    {
        return 'note.shared';
    }

    public function broadcastWith()
    {
        return [
            'note_id' => $this->note->id,
            'title' => $this->note->title ?: 'Untitled',
            'content' => $this->note->content,
            'notification' => $this->notificationData,
            'updated_at' => optional($this->note->updated_at)->toISOString(),
        ];
    }
}