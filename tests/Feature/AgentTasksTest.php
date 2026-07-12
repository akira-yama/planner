<?php

use App\Enums\AgentTaskStatus;
use App\Enums\TeamRole;
use App\Models\AgentTask;
use App\Models\Team;
use App\Models\User;
use App\Services\AgentService;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $response = $this->get('/team/tasks');
    $response->assertRedirect(route('login'));
});

test('authenticated users can view the tasks index', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $response = $this
        ->actingAs($user)
        ->get(route('agent-tasks.index', ['current_team' => $team->slug]));

    $response->assertOk();
});

test('users can create a task via service', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $service = new AgentService();
    $task = $service->createTask($team, [
        'title' => 'Test Task',
        'description' => 'Test description',
    ]);

    $this->assertDatabaseHas('agent_tasks', [
        'team_id' => $team->id,
        'title' => 'Test Task',
        'status' => AgentTaskStatus::Pending->value,
    ]);
});

test('users can view a task', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $task = AgentTask::factory()->create(['team_id' => $team->id]);

    $response = $this
        ->actingAs($user)
        ->get(route('agent-tasks.show', ['current_team' => $team->slug, 'agentTask' => $task->id]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('agent-tasks/Show')
        ->has('task')
        ->where('task.title', $task->title)
    );
});

test('users can update a task via service', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $task = AgentTask::factory()->create(['team_id' => $team->id]);

    $service = new AgentService();
    $service->updateTask($task, [
        'title' => 'Updated Task Title',
        'description' => 'Updated description',
        'status' => AgentTaskStatus::Completed->value,
    ]);

    $this->assertDatabaseHas('agent_tasks', [
        'id' => $task->id,
        'title' => 'Updated Task Title',
        'status' => AgentTaskStatus::Completed->value,
    ]);
});

test('users can delete a task via service', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $task = AgentTask::factory()->create(['team_id' => $team->id]);

    $service = new AgentService();
    $service->deleteTask($task);

    $this->assertDatabaseMissing('agent_tasks', ['id' => $task->id]);
});

test('tasks can be assigned to users via service', function () {
    $user = User::factory()->create();
    $assignee = User::factory()->create();
    $team = $user->currentTeam;

    $team->members()->attach($assignee, ['role' => TeamRole::Member->value]);

    $service = new AgentService();
    $task = $service->createTask($team, [
        'title' => 'Assigned Task',
        'assigned_to' => $assignee->id,
    ]);

    $this->assertDatabaseHas('agent_tasks', [
        'team_id' => $team->id,
        'assigned_to' => $assignee->id,
    ]);
});
