# Agent Docs & Task Management Service

A Laravel service for managing agent documentation and task tracking with MCP and HTTP API integration.

## Installation

Migrations are included in `database/migrations/`. Run them:

```bash
php artisan migrate
```

## Quick Start

### MCP (Model Context Protocol)

Start the MCP server for AI agent integration:

```bash
php artisan boost:mcp
```

Or use the built-in inspector:

```bash
php artisan mcp:inspector AgentServer
```

### HTTP API

```bash
# List available tools
GET /api/agent-tools

# Execute a tool
POST /api/agent-tools/execute
Content-Type: application/json

{
  "tool": "create_task",
  "parameters": {
    "team": "your-team-slug",
    "title": "New Task",
    "description": "Optional description"
  }
}
```

### PHP Service

```php
use App\Services\AgentService;

$service = app(AgentService::class);

// Create document
$doc = $service->createDoc($team, [
    'title' => 'API Guide',
    'content' => '# Documentation'
]);

// Create task
$task = $service->createTask($team, [
    'title' => 'Implement feature',
    'assigned_to' => $userId
]);

// Link task to document
$service->linkTaskToDoc($task, $doc);
```

## Available Tools

### MCP Tools

| Tool | Description | Parameters |
|------|-------------|------------|
| `list_team_docs_tool` | List team documents | `team_slug` (required) |
| `list_team_tasks_tool` | List team tasks | `team_slug` (required) |
| `create_task_tool` | Create a task | `team_slug`, `title`, `description`, `assigned_to`, `status` |
| `update_task_status_tool` | Update task status | `task_id`, `status` |

### HTTP Tools

| Tool | Description |
|------|-------------|
| `list_docs` | List all team documents |
| `get_doc` | Get specific document by slug |
| `create_task` | Create new task |
| `list_tasks` | List tasks with filters |
| `update_task_status` | Update task status |

## Task Status Values

- `pending`
- `in_progress`
- `review`
- `completed`
- `cancelled`

## Testing

```bash
php artisan test --compact --filter='AgentDocsTest|AgentTasksTest|AgentToolApiTest'
```

## Files

- `app/Services/AgentService.php` - Core service
- `app/Services/AgentToolService.php` - HTTP API service
- `app/Mcp/Servers/AgentServer.php` - MCP server
- `app/Mcp/Tools/AgentDocs/*` - Document tools
- `app/Mcp/Tools/AgentTasks/*` - Task tools
- `tests/Feature/AgentDocsTest.php` - Document tests
- `tests/Feature/AgentTasksTest.php` - Task tests
- `tests/Feature/AgentToolApiTest.php` - API tests