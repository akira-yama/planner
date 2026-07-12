<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgentDocs\CreateAgentDocRequest;
use App\Models\AgentDoc;
use App\Models\Team;
use App\Services\AgentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class AgentDocsController extends Controller
{
    public function __construct(protected AgentService $service) {}

    public function index(Team $currentTeam): Response
    {
        $docs = $this->service->getTeamDocs($currentTeam);

        return Inertia::render('agent-docs/Index', [
            'docs' => $docs,
        ]);
    }

    public function create(Team $currentTeam): Response
    {
        Gate::authorize('create', $currentTeam);

        return Inertia::render('agent-docs/Create', []);
    }

    public function store(CreateAgentDocRequest $request, Team $currentTeam): RedirectResponse
    {
        Gate::authorize('create', $currentTeam);

        $doc = $this->service->createDoc($currentTeam, $request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Document created.')]);

        return to_route('agent-docs.show', ['currentTeam' => $currentTeam->slug, 'agentDoc' => $doc->slug]);
    }

    public function show(Team $currentTeam, AgentDoc $agentDoc): Response
    {
        Gate::authorize('view', $agentDoc);

        $tasks = $this->service->getDocTasks($agentDoc);

        return Inertia::render('agent-docs/Show', [
            'doc' => $agentDoc,
            'tasks' => $tasks,
        ]);
    }

    public function edit(Team $currentTeam, AgentDoc $agentDoc): Response
    {
        Gate::authorize('update', $agentDoc);

        return Inertia::render('agent-docs/Edit', [
            'doc' => $agentDoc,
        ]);
    }

    public function update(CreateAgentDocRequest $request, Team $currentTeam, AgentDoc $agentDoc): RedirectResponse
    {
        Gate::authorize('update', $agentDoc);

        $this->service->updateDoc($agentDoc, $request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Document updated.')]);

        return to_route('agent-docs.show', ['currentTeam' => $currentTeam->slug, 'agentDoc' => $agentDoc->slug]);
    }

    public function destroy(Team $currentTeam, AgentDoc $agentDoc): RedirectResponse
    {
        Gate::authorize('delete', $agentDoc);

        $agentDoc->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Document deleted.')]);

        return to_route('agent-docs.index', $currentTeam->slug);
    }
}