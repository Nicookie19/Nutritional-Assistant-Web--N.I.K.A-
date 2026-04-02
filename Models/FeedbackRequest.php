<?php

namespace App\Models;

use Database\Factories\FeedbackRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_experience_id',
    'dietitian_id',
    'title',
    'topic',
    'tag',
    'tag_tone',
    'priority',
    'status',
    'message',
    'recommendations',
    'is_read',
    'submitted_on',
])]
class FeedbackRequest extends Model
{
    /** @use HasFactory<FeedbackRequestFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'recommendations' => 'array',
            'is_read' => 'boolean',
            'submitted_on' => 'date',
        ];
    }

    public function userExperience(): BelongsTo
    {
        return $this->belongsTo(UserExperience::class);
    }

    public function dietitian(): BelongsTo
    {
        return $this->belongsTo(Dietitian::class);
    }
}
