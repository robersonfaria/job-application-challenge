<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "address",
        "checked",
        "description",
        "interest",
        "date_of_birth",
        "email",
        "account",
    ];

    protected $casts = [
        "checked" => "boolean",
        "date_of_birth" => "date"
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    /**
     * @return int|null
     */
    public function getAgeAttribute(): int|null
    {
        return $this->date_of_birth->age ?? null;
    }
}
