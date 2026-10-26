<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageReply extends Model
{
    protected $fillable = [
        'contact_message_id',
        'admin_user_id',
        'reply_message',
        'is_read_by_user',
        'read_by_user_at'
    ];

    protected $casts = [
        'is_read_by_user' => 'boolean',
        'read_by_user_at' => 'datetime'
    ];

    public function contactMessage()
    {
        return $this->belongsTo(ContactMessage::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function markAsReadByUser()
    {
        $this->update([
            'is_read_by_user' => true,
            'read_by_user_at' => now()
        ]);
    }
}
