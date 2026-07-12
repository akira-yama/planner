<?php

namespace App\Mcp\Tools\AgentDocs;

use App\Services\AgentService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('List all documents for a team by slug.')]
class ListTeamDocsTool extends Tool
{
    public function __construct(protected AgentService $service) {}

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $teamSlug = $request->string('team_slug');

        $docs = $this->service->getTeamDocsBySlug($teamSlug);

        return Response::json($docs);
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
