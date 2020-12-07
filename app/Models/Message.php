<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $dates = [
        'created_at',
        'updated_at',
        // your other new column
    ];
    //
}
