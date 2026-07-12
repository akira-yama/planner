# Agent Docs & Task Management Service

A Laravel service for managing agent documentation and task tracking with MCP and HTTP API integration.

## Features

- Team-scoped documents and tasks
- Task assignment to team members
- Document-task linking (many-to-many)
- Task status workflow with auto-completion
- MCP (Model Context Protocol) integration
- HTTP API for programmatic access

## Installation

The service is already installed in this Laravel project. Ensure migrations are run:

```bash
php artisan migrate
```

## Quick Start

### HTTP API

```bash
# Get available tools
GET /api/agent-tools

# Create a task
POST /api/agent-tools/execute
Content-Type: application/json

{
  "tool": "create_task",
  "parameters": {
    "team": "your-team-slug",
    "title": "New Task",
    "description": "Task description"
  }
}
```

### MCP Integration

Connect via Laravel Boost MCP:

```bash
php artisan boost:mcp
```

Available tools:
- `list_team_docs_tool` - List all documents for a team
- `list_team_tasks_tool` - List all tasks for a team
- `create_task_tool` - Create a new task
- `update_task_status_tool` - Update task status

## Available Tools

### list_team_docs_tool

Lists all documents for a team.

**Parameters:**
| Name | Type | Required |
|------|------|----------|
| team_slug | string | Yes |

**Example:**
```json
{
  "team_slug": "engineering"
}
```

### list_team_tasks_tool

Lists all tasks for a team.

**Parameters:**
| Name | Type | Required |
|------|------|----------|
| team_slug | string | Yes |

**Example:**
```json
{
  "team_slug": "engineering"
}
```

### create_task_tool

Creates a new task.

**Parameters:**
| Name | Type | Required |
|------|------|----------|
| team_slug | string | Yes |
| title | string | Yes |
| description | string | No |
| assigned_to | integer | No |
| status | string | No (default: pending) |

**Example:**
```json
{
  "team_slug": "engineering",
  "title": "Review PR #123",
  "description": "Code review for the new feature",
  "assigned_to": 1,
  "status": "in_progress"
}
```

### update_task_status_tool

Updates a task's status.

**Parameters:**
| Name | Type | Required |
|------|------|----------|
| task_id | integer | Yes |
| status | string | Yes |

**Valid Status Values:**
- `pending`
- `in_progress`
- `review`
- `completed`
- `cancelled`

## Service Usage (PHP)

```php
use App\Services\AgentService;

$service = app(AgentService::class);

// Create a document
$doc = $service->createDoc($team, [
    'title' => 'API Documentation',
    'content' => '# My API Guide',
]);

// Create a task
$task = $service->createTask($team, [
    'title' => 'Implement feature',
    'description' => 'Build the new API endpoint',
    'assigned_to' => $userId,
]);

// Link task to document
$service->linkTaskToDoc($task, $doc);

// Get team docs/tasks
$docs = $service->getTeamDocs($team);
$tasks = $service->getTeamTasks($team);

// Update task (auto-sets completed_at when marked completed)
$service->updateTask($task, [
    'status' => 'completed',
]);
```

## Database Schema

### agent_docs
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| team_id | bigint | Foreign key to teams |
| title | string | Document title |
| slug | string | URL-friendly slug |
| content | text | Markdown/HTML content |
| version | string | Version string (default: 1.0) |
| metadata | json | Arbitrary key-value pairs |
| created_at | timestamp | Creation time |
| updated_at | timestamp | Last update time |

### agent_tasks
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| team_id | bigint | Foreign key to teams |
| assigned_to | bigint | Foreign key to users (nullable) |
| title | string | Task title |
| description | text | Task description (nullable) |
| status | string | Task status enum |
| due_at | timestamp | Due date (nullable) |
| completed_at | timestamp | Completion date (auto-set) |
| metadata | json | Arbitrary key-value pairs |
| created_at | timestamp | Creation time |
| updated_at | timestamp | Last update time |

### agent_task_doc (pivot)
| Column | Type | Description |
|--------|------|-------------|
| agent_task_id | bigint | Foreign key to tasks |
| agent_doc_id | bigint | Foreign key to documents |

## Frontend Pages

Located in `resources/js/pages/`:
- `agent-docs/Index.vue` - Document listing
- `agent-docs/Show.vue` - Document detail with related tasks
- `agent-tasks/Index.vue` - Task listing
- `agent-tasks/Show.vue` - Task detail with related documents

## Testing

```bash
# Run all agent tests
php artisan test --compact --filter='AgentDocsTest|AgentTasksTest|AgentToolApiTest'

# Run MCP server tests
php artisan mcp:inspector AgentServer
```