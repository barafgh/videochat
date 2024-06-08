<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallSummary extends Model
{
    use HasFactory;

    protected $fillable = ['caller_id', 'recipient_id', 'duration', 'status'];

    public function caller()
    {
        return $this->belongsTo(User::class, 'caller_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function scopeCallHistory($query, $userId)
    {
        return $query->where('caller_id', $userId)->orWhere('recipient_id', $userId);
    }

    public function scopeCallHistoryWith($query, $userId)
    {
        return $query->where('caller_id', $userId)->orWhere('recipient_id', $userId)->with(['caller', 'recipient']);
    }
}
