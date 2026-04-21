<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SharedNote extends Model
{
    //
     protected $fillable = [
        'note_id',  
        'owner_id',
        'recipient_id',
        'permission',
    ];

    public function note()
    {
        return $this->belongsTo(\App\Models\Note::class);
    }

    public function owner()
    {
        return $this->belongsTo(\App\Models\User::class, 'owner_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
    



