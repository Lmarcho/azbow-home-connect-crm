<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'previous_status',
        'new_status',
        'changed_by',
        'changed_at',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
