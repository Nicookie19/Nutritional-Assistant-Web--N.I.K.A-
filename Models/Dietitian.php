<?php

namespace App\Models;

use Database\Factories\DietitianFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'email',
    'specialization',
    'experience_years',
    'patient_count',
    'rating',
    'status',
])]
class Dietitian extends Model
{
    /** @use HasFactory<DietitianFactory> */
    use HasFactory;

    public function feedbackRequests(): HasMany
    {
        return $this->hasMany(FeedbackRequest::class);
    }
}
