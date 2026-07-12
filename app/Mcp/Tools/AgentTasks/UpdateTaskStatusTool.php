<?php

namespace App\Mcp\Tools\AgentTasks;

use App\Services\AgentService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Update the status of a task.')]
class UpdateTaskStatusTool extends Tool
{
    public function __construct(protected AgentService $service) {}

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $task = \App\Models\AgentTask::where('id', $request->integer('task_id'))->firstOrFail();

        $task->update(['status' => $request->string('status')]);

        return Response::json($task->fresh()->toArray());
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'task_id' => $schema->integer()->required(),
            'status' => $schema->string()->required(),
        ];
    }
}
