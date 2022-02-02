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

    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    public function setDateOfBirthAttribute($value)
    {
        try {
            if (!empty($value)) {
                $this->attributes['date_of_birth'] = Carbon::parse($value)->startOfDay();
            }
        } catch (\Exception $e) {
            if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $value)) {
                $this->attributes['date_of_birth'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
            } else {
                throw $e;
            }
        }
    }

    public function getAgeAttribute(): int|null
    {
        return $this->date_of_birth->age ?? null;
    }
}
