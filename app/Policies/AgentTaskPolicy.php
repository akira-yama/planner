<?php

namespace App\Policies;

use App\Enums\TeamPermission;
use App\Models\AgentTask;
use App\Models\Team;
use App\Models\User;

class AgentTaskPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, AgentTask $task): bool
    {
        return $user->belongsToTeam($task->team);
    }

    public function create(User $user, Team $task): bool
    {
        return $user->belongsToTeam($task);
    }

    public function update(User $user, AgentTask $task): bool
    {
        return $user->belongsToTeam($task->team) &&
            ($user->hasTeamPermission($task->team, TeamPermission::UpdateTeam) ||
             $user->hasTeamPermission($task->team, TeamPermission::UpdateMember) ||
             $task->assigned_to === $user->id);
    }

    public function delete(User $user, AgentTask $task): bool
    {
        return $user->hasTeamPermission($task->team, TeamPermission::UpdateTeam);
    }

    public function assign(User $user, AgentTask $task): bool
    {
        return $user->hasTeamPermission($task->team, TeamPermission::UpdateTeam) ||
            $user->hasTeamPermission($task->team, TeamPermission::UpdateMember);
    }
}