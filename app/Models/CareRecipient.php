<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\CareRecipientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareRecipient extends Model
{
    /** @use HasFactory<CareRecipientFactory> */
    use HasFactory;

    protected $fillable = [
        'babysitting_request_id',
        'name',
        'date_of_birth',
        'remarks',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function ($recipient) {
            if ($recipient->date_of_birth) {
                $now = Carbon::now();
                $dob = Carbon::parse($recipient->date_of_birth);
                $ageInMonths = $dob->diffInMonths($now, false);

                // Check if at least 1 month old
                if ($ageInMonths < 1) {
                    throw new \Exception('Child must be at least 1 month old');
                }

                // Check if at most 12 years and 11 months old (155 months)
                if ($ageInMonths > 155) {
                    throw new \Exception('Child must be at most 12 years and 11 months old');
                }
            }
        });
    }

    public function babysittingRequest(): BelongsTo
    {
        return $this->belongsTo(BabysittingRequest::class);
    }

    public function getAgeAttribute(): string
    {
        if ($this->date_of_birth) {
            $now = Carbon::now();
            $years = $now->diffInYears($this->date_of_birth);
            $months = $now->copy()->subYears($years)->diffInMonths($this->date_of_birth);

            if ($years > 0) {
                return $years . ' year' . ($years > 1 ? 's' : '') . ($months > 0 ? ', ' . $months . ' month' . ($months > 1 ? 's' : '') : '');
            }

            return $months . ' month' . ($months > 1 ? 's' : '');
        }

        return 'Unknown';
    }

    public function getAgeYearsAttribute(): int
    {
        if ($this->date_of_birth) {
            return Carbon::now()->diffInYears($this->date_of_birth);
        }

        return 0;
    }

    public function getAgeMonthsAttribute(): int
    {
        if ($this->date_of_birth) {
            $years = Carbon::now()->diffInYears($this->date_of_birth);
            return Carbon::now()->copy()->subYears($years)->diffInMonths($this->date_of_birth);
        }

        return 0;
    }
}
