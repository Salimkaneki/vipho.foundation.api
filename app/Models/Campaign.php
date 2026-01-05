<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [
        'title',
        'description',
        'goal_amount',
        'current_amount',
        'organization_name',
        'start_date',
        'end_date',
        'status',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }
}
