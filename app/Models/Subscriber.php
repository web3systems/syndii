<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscriber extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'subscribers';


    /**
     * Subscription belongs to a single user
     *
     * 
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Plan belongs to a single user
     *
     * 
     */
    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }


    /**
     * Check if subscription is active
     *
     * 
     */
    public function isActive($id)
    {
        $subscription = Subscriber::where('status', 'Active')->where('user_id', $id)->first();
        \Log::info($subscription);
        if ($subscription) {
            return Carbon::parse($this->active_until)->isPast();
        } 
    }
}
