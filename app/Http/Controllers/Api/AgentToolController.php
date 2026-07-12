<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AgentTools\ExecuteToolRequest;
use App\Services\AgentToolService;
use Illuminate\Http\JsonResponse;

class AgentToolController extends Controller
{
    public function __construct(protected AgentToolService $agentTools) {}

    /**
     * Get available tools.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->agentTools->getAvailableTools());
    }

    /**
     * Execute a tool.
     */
    public function execute(ExecuteToolRequest $request): JsonResponse
    {
        $result = $this->agentTools->executeTool(
            $request->validated('tool'),
            $request->validated('parameters', [])
        );

        return response()->json($result);
    }
}
