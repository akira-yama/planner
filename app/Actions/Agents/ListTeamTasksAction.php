<?php

namespace App\Actions\Agents;

use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

class ListTeamTasksAction
{
    /**
     * List tasks for a team.
     */
    public function handle(Team $team, array $filters = []): Collection
    {
        $query = $team->tasks()->with('assignee');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        return $query->latest()->get();
    }
}