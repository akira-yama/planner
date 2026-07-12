<?php

namespace App\Models;

use App\Enums\AgentTaskStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $team_id
 * @property int|null $assigned_to
 * @property string $title
 * @property string|null $description
 * @property string $status
 * @property Carbon|null $due_at
 * @property Carbon|null $completed_at
 * @property array|null $metadata
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Team $team
 * @property-read User|null $assignee
 * @property-read Collection<int, AgentDoc> $docs
 */
#[Fillable(['team_id', 'assigned_to', 'title', 'description', 'status', 'due_at', 'completed_at', 'metadata'])]
class AgentTask extends Model
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
            'due_at' => 'datetime',
            'completed_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    /**
     * Get the team that owns this task.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the user assigned to this task.
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the documents related to this task.
     *
     * @return BelongsToMany<AgentDoc, $this>
     */
    public function docs(): BelongsToMany
    {
        return $this->belongsToMany(AgentDoc::class, 'agent_task_doc', 'agent_task_id', 'agent_doc_id')
            ->withTimestamps();
    }

    /**
     * Check if the task is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === AgentTaskStatus::Completed->value;
    }

    /**
     * Check if the task is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->due_at && $this->due_at->isPast() && ! $this->isCompleted();
    }
}