<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    protected $fillable = ['description', 'completed_at', 'used_time', 'estimated_time', 'user_id'];

    /** @return BelongsTo<User, Task> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function setCompleted(): Task
    {
        $this->completed_at = Carbon::now();
        $this->save();

        return $this;
    }

    public function setUnCompleted(): self
    {
        $this->completed_at = null;
        $this->save();

        return $this;
    }

    public function isCompleted(): bool
    {
        return !is_null($this->completed_at);
    }

    public function getCreatedAtAttribute(?string $value): ?string
    {
        return $value
            ? Carbon::parse($value)->format('Y-m-d H:i:s')
            : null;
    }

    public function getUpdatedAtAttribute(?string $value): ?string
    {
        return $value
            ? Carbon::parse($value)->format('Y-m-d H:i:s')
            : null;
    }
}
