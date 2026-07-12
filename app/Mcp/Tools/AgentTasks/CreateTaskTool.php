<?php

namespace App\Mcp\Tools\AgentTasks;

use App\Enums\AgentTaskStatus;
use App\Services\AgentService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Create a new task for a team.')]
class CreateTaskTool extends Tool
{
    public function __construct(protected AgentService $service) {}

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $teamSlug = $request->string('team_slug');
        $team = \App\Models\Team::where('slug', $teamSlug)->firstOrFail();

        $task = $this->service->createTask($team, [
            'title' => $request->string('title'),
            'description' => $request->string('description')->value(),
            'assigned_to' => $request->integer('assigned_to')->value(),
            'status' => $request->string('status')->value() ?? AgentTaskStatus::Pending->value,
        ]);

        return Response::json($task->toArray());
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'team_slug' => $schema->string()->required(),
            'title' => $schema->string()->required(),
            'description' => $schema->string(),
            'assigned_to' => $schema->integer(),
            'status' => $schema->string(),
        ];
    }
}
