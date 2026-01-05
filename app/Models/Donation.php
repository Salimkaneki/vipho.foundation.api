<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = [
        'type',
        'amount',
        'currency',
        'description',
        'quantity',
        'category',
        'message',
        'is_anonymous',
        'donor_id',
        'donor_name',
        'donor_email',
        'campaign_id',
        'payment_method',
        'status',
        'first_name',
        'last_name',
        'phone',
        'payment_type',
        'deposit_number',
        'location',
    ];

    public function donor()
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
