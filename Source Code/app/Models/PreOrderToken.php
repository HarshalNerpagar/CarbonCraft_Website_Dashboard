<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PreOrderToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'agent_id',
        'payment_method',
        'advance_amount',
        'customer_phone',
        'customer_name',
        'used',
        'order_id',
        'expires_at',
        'used_at',
        'notes',
    ];

    protected $casts = [
        'advance_amount' => 'decimal:2',
        'used' => 'boolean',
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    /**
     * Generate a unique token (excludes ambiguous characters: I, l, O, 0, 1)
     */
    public static function generateUniqueToken(): string
    {
        do {
            // Use characters that are easy to distinguish
            // Excludes: I, l, O, 0, 1 to avoid confusion
            $characters = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
            $token = '';
            for ($i = 0; $i < 32; $i++) {
                $token .= $characters[rand(0, strlen($characters) - 1)];
            }
        } while (self::where('token', $token)->exists());

        return $token;
    }

    /**
     * Check if token is valid (not used and not expired)
     */
    public function isValid(): bool
    {
        if ($this->used) {
            return false;
        }

        if ($this->expires_at && Carbon::now()->greaterThan($this->expires_at)) {
            return false;
        }

        return true;
    }

    /**
     * Mark token as used
     */
    public function markAsUsed($orderId): void
    {
        $this->update([
            'used' => true,
            'used_at' => now(),
            'order_id' => $orderId,
        ]);
    }

    /**
     * Get the agent who created this token
     */
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /**
     * Get the order created from this token
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Scope for active tokens (not used and not expired)
     */
    public function scopeActive($query)
    {
        return $query->where('used', false)
                     ->where(function($q) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>', now());
                     });
    }

    /**
     * Scope for expired tokens
     */
    public function scopeExpired($query)
    {
        return $query->where('used', false)
                     ->where('expires_at', '<', now());
    }
}
