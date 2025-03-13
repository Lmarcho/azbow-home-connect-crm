<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_info',
        'source',
        'inquiry_date',
        'status',
        'assigned_agent_id',
        'budget',
        'location_preference',
        'property_interests'
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(LeadStatusLog::class);
    }
}
