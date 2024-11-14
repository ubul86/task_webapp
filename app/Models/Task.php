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
}
