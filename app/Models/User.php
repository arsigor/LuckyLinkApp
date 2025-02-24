<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_name',
        'phone_number',
        'unique_link',
        'link_expires_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'link_expires_at' => 'datetime',
    ];

    /**
     * Generate a unique link for the user and set the expiration date.
     *
     * @return void
     */
    public function generateUniqueLink(): void
    {
        $this->update([
            'unique_link' => uniqid('link_', true),
            'link_expires_at' => Carbon::now()->addDays(7),
        ]);
    }

    /**
     * Check if the user's unique link is still valid.
     *
     * @return bool
     */
    public function isLinkValid(): bool
    {
        return $this->link_expires_at > Carbon::now();
    }

    /**
     * Get the lucky results for the user.
     *
     * @return HasMany
     */
    public function luckyResults()
    {
        return $this->hasMany(LuckyResult::class);
    }

    /**
     * Mutator for phone_number - normalizes phone number to international format.
     *
     * @param  string  $value
     * @return void
     */
    public function setPhoneNumberAttribute($value)
    {
        // Remove all non-numeric characters, except for the leading '+'
        $normalizedPhoneNumber = preg_replace('/[^0-9+]/', '', $value);

        // Set the normalized phone number
        $this->attributes['phone_number'] = $normalizedPhoneNumber;
    }
}
