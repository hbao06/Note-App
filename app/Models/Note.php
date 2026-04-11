<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    //
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'is_pinned',
        'pinned_at',
        'is_locked',
        'locked_password',
    ];

    // 1 note thuộc về 1 user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 1 note có nhiều ảnh
    public function images()
    {
        return $this->hasMany(NoteImage::class);
    }

    // Note có nhiều labels
    public function labels()
    {
        return $this->belongsToMany(Label::class, 'label_note');
    }

    // Note có nhiều người chia sẻ
    public function shared()
    {
        return $this->hasMany(SharedNote::class);
    }
}
