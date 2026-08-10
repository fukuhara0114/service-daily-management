<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'work_date',
    'new_case_count',
    'mail_notice_count',
    'remarks',
])]
class DailyActivity extends Model
{
    protected function casts(): array
    {
        return [
            'work_date' => 'date',
            'new_case_count' => 'integer',
            'mail_notice_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
