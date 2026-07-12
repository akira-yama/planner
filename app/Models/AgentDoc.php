<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property int $team_id
 * @property string $title
 * @property string $slug
 * @property string|null $content
 * @property string $version
 * @property array|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Team $team
 * @property-read Collection<int, AgentTask> $tasks
 */
#[Fillable(['team_id', 'title', 'slug', 'content', 'version', 'metadata'])]
class AgentDoc extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    /**
     * Get the team that owns this document.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the tasks related to this document.
     *
     * @return BelongsToMany<AgentTask, $this>
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(AgentTask::class, 'agent_task_doc', 'agent_doc_id', 'agent_task_id')
            ->withTimestamps();
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}