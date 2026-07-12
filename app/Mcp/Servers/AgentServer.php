<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\AgentDocs\ListTeamDocsTool;
use App\Mcp\Tools\AgentTasks\CreateTaskTool;
use App\Mcp\Tools\AgentTasks\ListTeamTasksTool;
use App\Mcp\Tools\AgentTasks\UpdateTaskStatusTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Agent Server')]
#[Version('0.0.1')]
#[Instructions('Provides tools for managing agent documentation and tasks. Use team_slug to identify which team to work with.')]
class AgentServer extends Server
{
    protected array $tools = [
        ListTeamDocsTool::class,
        ListTeamTasksTool::class,
        CreateTaskTool::class,
        UpdateTaskStatusTool::class,
    ];

    protected array $resources = [
        //
    ];

    protected array $prompts = [
        //
    ];
}
