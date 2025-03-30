<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\BabysittingRequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BabysittingRequest extends Model
{
    /** @use HasFactory<BabysittingRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'handled_by',
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'start_datetime',
        'end_datetime',
        'message',
        'care_recipient_count',
        'status',
        'booking_number',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($request) {
            if (empty($request->booking_number)) {
                $request->booking_number = 'BKS-' . date('Ymd') . '-' . random_int(1000, 9999);
            }
        });
    }

    public function hasValidStartTime(): bool
    {
        $now = Carbon::now();
        $minTime = $now->copy()->addHours(6);
        $maxTime = $now->copy()->addDays(30);

        return $this->start_datetime->greaterThanOrEqualTo($minTime) &&
            $this->start_datetime->lessThanOrEqualTo($maxTime);
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function careRecipients(): HasMany
    {
        return $this->hasMany(CareRecipient::class);
    }

    public function statusRelation(): BelongsTo
    {
        return $this->belongsTo(BabysittingRequestStatus::class, 'status', 'name');
    }

    public function addCareRecipient(array $data): CareRecipient
    {
        if ($this->careRecipients()->count() >= 4) {
            throw new \Exception('Maximum of 4 children allowed per request');
        }

        return $this->careRecipients()->create($data);
    }
}
