<?php

namespace App\Policies;

use App\Enums\TeamPermission;
use App\Models\AgentDoc;
use App\Models\Team;
use App\Models\User;

class AgentDocPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, AgentDoc $doc): bool
    {
        return $user->belongsToTeam($doc->team);
    }

    public function create(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    public function update(User $user, AgentDoc $doc): bool
    {
        return $user->hasTeamPermission($doc->team, TeamPermission::UpdateTeam);
    }

    public function delete(User $user, AgentDoc $doc): bool
    {
        return $user->hasTeamPermission($doc->team, TeamPermission::UpdateTeam);
    }
}