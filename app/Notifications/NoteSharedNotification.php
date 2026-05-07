<?php

namespace App\Notifications;

use App\Models\Note;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NoteSharedNotification extends Notification
{
    use Queueable;

    public $note;
    public $owner;

    public function __construct(Note $note, User $owner)
    {
        $this->note = $note;
        $this->owner = $owner;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $sharedUrl = url('/notes/shared');

        return (new MailMessage)
            ->subject('Một ghi chú đã được chia sẻ với bạn')
            ->greeting('Xin chào ' . $notifiable->name)
            ->line($this->owner->name . ' đã chia sẻ một ghi chú với bạn.')
            ->line('Tiêu đề: ' . ($this->note->title ?: 'Untitled'))
            ->action('Mở ghi chú', $sharedUrl)
            ->line('Bạn có thể xem ghi chú trong mục Shared with me.');
    }

    public function toArray($notifiable)
    {
        return [
            'note_id' => $this->note->id,
            'note_title' => $this->note->title ?: 'Untitled',
            'owner_id' => $this->owner->id,
            'owner_name' => $this->owner->name,
            'message' => $this->owner->name . ' đã chia sẻ ghi chú "' . ($this->note->title ?: 'Untitled') . '" với bạn.',
            'url' => url('/notes/shared'),
        ];
    }
}