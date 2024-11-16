<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Task;

/**
 * @property Task $resource
 */
class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'user_id' => $this->resource->user_id,
            'description' => $this->resource->description,
            'estimated_time' => $this->resource->estimated_time,
            'used_time' => $this->resource->used_time,
            'completed_at' => $this->resource->completed_at,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
            'user_name' => $this->resource->user->name ?? '-',
            'is_completed' => $this->resource->completed_at ? true : false,
        ];
    }
}
