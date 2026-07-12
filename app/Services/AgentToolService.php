<?php

namespace App\Services;

use App\Actions\Agents\ListTeamDocsAction;
use App\Actions\Agents\ListTeamTasksAction;
use App\Enums\AgentTaskStatus;
use App\Models\AgentDoc;
use App\Models\AgentTask;
use App\Models\Team;
use Illuminate\Support\Facades\Http;

/**
 * Service for agent tool integration.
 * Provides structured access to agent docs/tasks and external MCP tools.
 */
class AgentToolService
{
    /**
     * Get available tools for agents.
     */
    public function getAvailableTools(): array
    {
        return [
            'list_docs' => [
                'description' => 'List all team documents',
                'parameters' => ['team' => 'string'],
            ],
            'get_doc' => [
                'description' => 'Get a specific document by slug',
                'parameters' => ['team' => 'string', 'slug' => 'string'],
            ],
            'create_task' => [
                'description' => 'Create a new task',
                'parameters' => [
                    'team' => 'string',
                    'title' => 'string',
                    'description' => 'string',
                    'assign_to' => 'integer|nullable',
                ],
            ],
            'list_tasks' => [
                'description' => 'List team tasks with optional filters',
                'parameters' => [
                    'team' => 'string',
                    'status' => 'string|optional',
                    'assignee' => 'integer|optional',
                ],
            ],
            'update_task_status' => [
                'description' => 'Update task status',
                'parameters' => [
                    'team' => 'string',
                    'task_id' => 'integer',
                    'status' => 'enum:'.implode(',', AgentTaskStatus::values()),
                ],
            ],
            'mcp_tools' => [
                'description' => 'Access MCP (Model Context Protocol) tools',
                'parameters' => ['action' => 'string', 'payload' => 'object'],
            ],
        ];
    }

    /**
     * Execute a tool action.
     */
    public function executeTool(string $tool, array $parameters): array
    {
        return match ($tool) {
            'list_docs' => $this->listDocs($parameters['team']),
            'get_doc' => $this->getDoc($parameters['team'], $parameters['slug']),
            'create_task' => $this->createTask($parameters),
            'list_tasks' => $this->listTasks($parameters),
            'update_task_status' => $this->updateTaskStatus($parameters),
            'mcp_tools' => $this->callMcpTool($parameters),
            default => throw new \InvalidArgumentException("Unknown tool: {$tool}"),
        };
    }

    protected function listDocs(string $teamSlug): array
    {
        $team = Team::where('slug', $teamSlug)->firstOrFail();

        return (new ListTeamDocsAction())->handle($team)->toArray();
    }

    protected function getDoc(string $teamSlug, string $docSlug): array
    {
        $doc = AgentDoc::where('slug', $docSlug)
            ->whereHas('team', fn ($q) => $q->where('slug', $teamSlug))
            ->with('tasks')
            ->firstOrFail();

        return $doc->toArray();
    }

    protected function createTask(array $data): array
    {
        $team = Team::where('slug', $data['team'])->firstOrFail();

        $task = $team->tasks()->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => AgentTaskStatus::Pending->value,
            'assigned_to' => $data['assign_to'] ?? null,
        ]);

        return $task->toArray();
    }

    protected function listTasks(array $data): array
    {
        $team = Team::where('slug', $data['team'])->firstOrFail();

        $filters = array_filter([
            'status' => $data['status'] ?? null,
            'assignee' => $data['assignee'] ?? null,
        ]);

        return (new ListTeamTasksAction())->handle($team, $filters)->toArray();
    }

    protected function updateTaskStatus(array $data): array
    {
        $team = Team::where('slug', $data['team'])->firstOrFail();
        $task = AgentTask::where('id', $data['task_id'])
            ->where('team_id', $team->id)
            ->firstOrFail();

        $task->update(['status' => $data['status']]);

        return $task->fresh()->toArray();
    }

    /**
     * Call MCP tool via HTTP (for external MCP servers).
     */
    protected function callMcpTool(array $parameters): array
    {
        $mcpEndpoint = config('services.mcp.endpoint');

        if (! $mcpEndpoint) {
            return ['error' => 'MCP endpoint not configured'];
        }

        $response = Http::post($mcpEndpoint, $parameters);

        return $response->json() ?? [];
    }
}