<?php

namespace App\Actions\Agents;

use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

class ListTeamDocsAction
{
    /**
     * List documents for a team.
     */
    public function handle(Team $team): Collection
    {
        return $team->docs()->latest()->get();
    }
}