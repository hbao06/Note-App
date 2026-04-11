<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    //
     protected $fillable = [
        'user_id',
        'name',
    ];

    // Label thuộc về 1 user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Label có nhiều notes
    public function notes()
    {
        return $this->belongsToMany(Note::class, 'label_note');
    }
}
