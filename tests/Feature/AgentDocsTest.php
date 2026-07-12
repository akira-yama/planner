<?php

use App\Models\AgentDoc;
use App\Models\Team;
use App\Models\User;
use App\Services\AgentService;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $response = $this->get('/team/docs');
    $response->assertRedirect(route('login'));
});

test('authenticated users can view the docs index', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $response = $this
        ->actingAs($user)
        ->get(route('agent-docs.index', ['current_team' => $team->slug]));

    $response->assertOk();
});

test('users can create a document via service', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $service = new AgentService();
    $doc = $service->createDoc($team, [
        'title' => 'Test Document',
        'content' => '# Test Content',
    ]);

    $this->assertDatabaseHas('agent_docs', [
        'team_id' => $team->id,
        'title' => 'Test Document',
        'slug' => 'test-document',
    ]);
});

test('users can view a document', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $doc = AgentDoc::factory()->create(['team_id' => $team->id]);

    $response = $this
        ->actingAs($user)
        ->get(route('agent-docs.show', ['current_team' => $team->slug, 'agentDoc' => $doc->slug]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('agent-docs/Show')
        ->has('doc')
        ->where('doc.title', $doc->title)
    );
});

test('users can update a document via service', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $doc = AgentDoc::factory()->create(['team_id' => $team->id]);

    $service = new AgentService();
    $service->updateDoc($doc, [
        'title' => 'Updated Title',
        'content' => 'Updated content',
    ]);

    $this->assertDatabaseHas('agent_docs', [
        'id' => $doc->id,
        'title' => 'Updated Title',
        'slug' => 'updated-title',
    ]);
});

test('users can delete a document via service', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $doc = AgentDoc::factory()->create(['team_id' => $team->id]);

    $service = new AgentService();
    $service->deleteDoc($doc);

    $this->assertDatabaseMissing('agent_docs', ['id' => $doc->id]);
});
