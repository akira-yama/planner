<?php

namespace App\Services;

use App\Enums\AgentTaskStatus;
use App\Models\AgentDoc;
use App\Models\AgentTask;
use App\Models\Team;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class AgentService
{
    /**
     * Get all documents for a team.
     *
     * @return LengthAwarePaginator<AgentDoc>
     */
    public function getTeamDocs(Team $team, int $perPage = 15): LengthAwarePaginator
    {
        return AgentDoc::where('team_id', $team->id)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get all documents for a team by slug.
     */
    public function getTeamDocsBySlug(string $teamSlug, int $perPage = 15): array
    {
        $team = Team::where('slug', $teamSlug)->firstOrFail();

        return AgentDoc::where('team_id', $team->id)
            ->latest()
            ->get()
            ->toArray();
    }

    /**
     * Get all tasks for a team.
     *
     * @return LengthAwarePaginator<AgentTask>
     */
    public function getTeamTasks(Team $team, int $perPage = 15): LengthAwarePaginator
    {
        return AgentTask::where('team_id', $team->id)
            ->with('assignee')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new document.
     */
    public function createDoc(Team $team, array $data): AgentDoc
    {
        $slug = $data['slug'] ?? str($data['title'])->slug();

        return $team->docs()->create([
            'title' => $data['title'],
            'slug' => $slug,
            'content' => $data['content'] ?? null,
            'version' => $data['version'] ?? '1.0',
            'metadata' => $data['metadata'] ?? null,
        ]);
    }

    /**
     * Create a new task.
     */
    public function createTask(Team $team, array $data): AgentTask
    {
        return $team->tasks()->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? AgentTaskStatus::Pending->value,
            'assigned_to' => $data['assigned_to'] ?? null,
            'due_at' => $data['due_at'] ?? null,
            'metadata' => $data['metadata'] ?? null,
        ]);
    }

    /**
     * Update an existing document.
     */
    public function updateDoc(AgentDoc $doc, array $data): AgentDoc
    {
        if (isset($data['title']) && !isset($data['slug'])) {
            $data['slug'] = str($data['title'])->slug();
        }

        $doc->update($data);

        return $doc->fresh();
    }

    /**
     * Update an existing task.
     */
    public function updateTask(AgentTask $task, array $data): AgentTask
    {
        $wasCompleted = $task->isCompleted();
        $willBeCompleted = ($data['status'] ?? $task->status) === AgentTaskStatus::Completed->value;

        $task->update($data);

        // Auto-set completed_at when task is marked completed
        if ($willBeCompleted && ! $wasCompleted) {
            $task->update(['completed_at' => now()]);
        }

        // Clear completed_at if task is reopened
        if (! $willBeCompleted && $wasCompleted && $task->completed_at) {
            $task->update(['completed_at' => null]);
        }

        return $task->fresh();
    }

    /**
     * Link a task to a document.
     */
    public function linkTaskToDoc(AgentTask $task, AgentDoc $doc): void
    {
        $task->docs()->syncWithoutDetaching([$doc->id]);
    }

    /**
     * Unlink a task from a document.
     */
    public function unlinkTaskFromDoc(AgentTask $task, AgentDoc $doc): void
    {
        $task->docs()->detach($doc->id);
    }

    /**
     * Get tasks related to a document.
     */
    public function getDocTasks(AgentDoc $doc): Collection
    {
        return $doc->tasks()->with('assignee')->get();
    }

    /**
     * Get documents related to a task.
     */
    public function getTaskDocs(AgentTask $task): Collection
    {
        return $task->docs()->get();
    }

    /**
     * Get available users for task assignment (team members).
     */
    public function getAssignableUsers(Team $team): Collection
    {
        return $team->members()->get();
    }

    /**
     * Delete a document.
     */
    public function deleteDoc(AgentDoc $doc): bool
    {
        return $doc->delete();
    }

    /**
     * Delete a task.
     */
    public function deleteTask(AgentTask $task): bool
    {
        return $task->delete();
    }
}