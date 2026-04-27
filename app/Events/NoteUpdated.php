<?php

namespace App\Events;

use App\Models\Note;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class NoteUpdated implements ShouldBroadcast
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
        return [
            'id' => $this->note->id,
            'title' => $this->note->title,
            'content' => $this->note->content,
            'updated_by' => $this->userId,
            'updated_at' => $this->note->updated_at->toISOString(),
        ];
    }
}