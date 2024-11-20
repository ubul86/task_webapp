<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use App\Models\Task;
use Illuminate\Support\Collection;

class TaskFilter
{
    protected array $params = [];

    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    /**
     *
     * @param Builder<Task> $query
     * @return Builder<Task>
     */
    public function apply(Builder $query): Builder
    {

        $params = new Collection($this->params['filters'] ?? []);

        $this->applySort($query);

        if ($params->has('search')) {
            $query->where('name', 'like', '%' . $params->get('search') . '%');
        }

        if ($params->has('is_completed')) {
            $isCompleted = $params->get('is_completed');

            if (count($isCompleted) === 1) {
                if ($isCompleted[0] === 'none') {
                    $query->whereRaw('1 = 0');
                } else {
                    $query->where('is_completed', filter_var($isCompleted[0], FILTER_VALIDATE_BOOLEAN));
                }
            }
        }

        if (!empty($this->params['user_name'])) {
            $query->whereHas('user', function (Builder $subQuery) {
                $subQuery->where('name', 'like', '%' . $this->params['user_name'] . '%');
            });
        }

        return $query;
    }

    /**
     * @param Builder<Task> $query
     * @return Builder<Task>
     */
    protected function applySort(Builder $query): Builder
    {
        if (!empty($this->params['sortBy'])) {
            foreach ($this->params['sortBy'] as $sort) {
                $this->applySingleSort($query, $sort);
            }
        }
        return $query;
    }

    /**
     * @param Builder<Task> $query
     * @param array $sort
     */
    protected function applySingleSort(Builder $query, array $sort): void
    {
        $key = $sort['key'];
        $order = $sort['order'] ?? 'asc';

        switch ($key) {
            case 'id':
            case 'is_completed':
            case 'created_at':
            case 'updated_at':
            case 'description':
            case 'estimated_time':
            case 'used_time':
            case 'user_id':
                $query->orderBy($key, $order);
                break;
            case 'user_name':
                $query->leftJoin('users', 'users.id', '=', 'tasks.user_id')
                    ->select('tasks.*', 'users.name as user_name')
                    ->orderBy('users.name', $order);
                break;

            default:
                throw new \InvalidArgumentException("Invalid sort key: {$key}");
        }
    }
}
