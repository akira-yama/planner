<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgentTasks\CreateAgentTaskRequest;
use App\Http\Requests\AgentTasks\UpdateAgentTaskRequest;
use App\Models\AgentTask;
use App\Models\Team;
use App\Services\AgentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class AgentTasksController extends Controller
{
    public function __construct(protected AgentService $service) {}

    public function index(Team $currentTeam): Response
    {
        $tasks = $this->service->getTeamTasks($currentTeam);

        return Inertia::render('agent-tasks/Index', [
            'tasks' => $tasks,
            'statusOptions' => \App\Enums\AgentTaskStatus::options(),
        ]);
    }

    public function create(Team $currentTeam): Response
    {
        Gate::authorize('create', $currentTeam);

        $assignableUsers = $this->service->getAssignableUsers($currentTeam);

        return Inertia::render('agent-tasks/Create', [
            'assignableUsers' => $assignableUsers,
            'statusOptions' => \App\Enums\AgentTaskStatus::options(),
        ]);
    }

    public function store(CreateAgentTaskRequest $request, Team $currentTeam): RedirectResponse
    {
        Gate::authorize('create', $currentTeam);

        $task = $this->service->createTask($currentTeam, $request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Task created.')]);

        return to_route('agent-tasks.show', ['currentTeam' => $currentTeam->slug, 'agentTask' => $task->id]);
    }

    public function show(Team $currentTeam, AgentTask $agentTask): Response
    {
        Gate::authorize('view', $agentTask);

        $docs = $this->service->getTaskDocs($agentTask);
        $assignableUsers = $this->service->getAssignableUsers($currentTeam);

        return Inertia::render('agent-tasks/Show', [
            'task' => $agentTask->load('assignee'),
            'docs' => $docs,
            'assignableUsers' => $assignableUsers,
            'statusOptions' => \App\Enums\AgentTaskStatus::options(),
        ]);
    }

    public function edit(Team $currentTeam, AgentTask $agentTask): Response
    {
        Gate::authorize('update', $agentTask);

        $assignableUsers = $this->service->getAssignableUsers($currentTeam);

        return Inertia::render('agent-tasks/Edit', [
            'task' => $agentTask->load('assignee'),
            'assignableUsers' => $assignableUsers,
            'statusOptions' => \App\Enums\AgentTaskStatus::options(),
        ]);
    }

    public function update(UpdateAgentTaskRequest $request, Team $currentTeam, AgentTask $agentTask): RedirectResponse
    {
        Gate::authorize('update', $agentTask);

        $this->service->updateTask($agentTask, $request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Task updated.')]);

        return to_route('agent-tasks.show', ['currentTeam' => $currentTeam->slug, 'agentTask' => $agentTask->id]);
    }

    public function destroy(Team $currentTeam, AgentTask $agentTask): RedirectResponse
    {
        Gate::authorize('delete', $agentTask);

        $agentTask->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Task deleted.')]);

        return to_route('agent-tasks.index', $currentTeam->slug);
    }
}