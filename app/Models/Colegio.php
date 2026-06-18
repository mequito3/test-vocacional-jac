<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Colegio extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'token'];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->token ??= Str::random(12);
        });
    }

    public function estudiantes(): HasMany
    {
        return $this->hasMany(Estudiante::class);
    }
}
