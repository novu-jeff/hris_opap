<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageAttachments extends Model
{
    use HasFactory;

    protected $table = 'messages_attachments';
    protected $fillable = [
        'message_id',
        'original',
        'attachment',
    ];

}
