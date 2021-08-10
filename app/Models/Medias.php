<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medias extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'conversationId',
        'audio',
        'image',
        'channel',
        'type'
    ];
}
