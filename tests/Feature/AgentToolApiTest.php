<?php

use App\Models\User;
use App\Services\AgentToolService;

test('users can get available tools', function () {
    $service = new AgentToolService();
    $tools = $service->getAvailableTools();

    expect($tools)->toHaveKeys(['list_docs', 'get_doc', 'create_task', 'list_tasks', 'update_task_status']);
});

test('users can list team tasks via service', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $service = new AgentToolService();
    $result = $service->executeTool('list_tasks', ['team' => $team->slug]);

    expect($result)->toBeArray();
    expect($result)->toBe([]);
});

test('users can create task via service', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $service = new AgentToolService();
    $result = $service->executeTool('create_task', [
        'team' => $team->slug,
        'title' => 'Test Task',
        'description' => 'Test description',
    ]);

    expect($result)->toHaveKeys(['id', 'team_id', 'title', 'status']);
    expect($result['title'])->toBe('Test Task');
});
