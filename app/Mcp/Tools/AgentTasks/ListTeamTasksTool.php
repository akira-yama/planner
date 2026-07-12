<?php

namespace App\Mcp\Tools\AgentTasks;

use App\Services\AgentService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('List all tasks for a team by slug with optional filters.')]
class ListTeamTasksTool extends Tool
{
    public function __construct(protected AgentService $service) {}

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $teamSlug = $request->string('team_slug');

        $team = \App\Models\Team::where('slug', $teamSlug)->firstOrFail();

        $tasks = $this->service->getTeamTasks($team)->items();

        return Response::json(array_map(fn ($task) => $task->toArray(), $tasks));
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
        ];
    }
}
