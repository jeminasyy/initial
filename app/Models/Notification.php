<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'user_id',
        'ticketId',
        'reopenId',
        'clicked'
    ];

    // Relationship to User
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
