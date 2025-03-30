<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BabysittingRequestStatus extends Model
{
    protected $fillable = ['name', 'label', 'color'];

    public static function values(): array
    {
        return self::pluck('name')->toArray();
    }

    public static function findByName(string $name): ?self
    {
        return self::where('name', $name)->first();
    }
}
