<?php

use App\Http\Controllers\AgentDocsController;
use App\Http\Controllers\AgentTasksController;
use App\Http\Controllers\Api\AgentToolController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');

        // Agent Docs routes
        Route::get('docs', [AgentDocsController::class, 'index'])->name('agent-docs.index');
        Route::get('docs/create', [AgentDocsController::class, 'create'])->name('agent-docs.create');
        Route::post('docs', [AgentDocsController::class, 'store'])->name('agent-docs.store');
        Route::get('docs/{agentDoc}', [AgentDocsController::class, 'show'])->name('agent-docs.show');
        Route::get('docs/{agentDoc}/edit', [AgentDocsController::class, 'edit'])->name('agent-docs.edit');
        Route::put('docs/{agentDoc}', [AgentDocsController::class, 'update'])->name('agent-docs.update');
        Route::delete('docs/{agentDoc}', [AgentDocsController::class, 'destroy'])->name('agent-docs.destroy');

        // Agent Tasks routes
        Route::get('tasks', [AgentTasksController::class, 'index'])->name('agent-tasks.index');
        Route::get('tasks/create', [AgentTasksController::class, 'create'])->name('agent-tasks.create');
        Route::post('tasks', [AgentTasksController::class, 'store'])->name('agent-tasks.store');
        Route::get('tasks/{agentTask}', [AgentTasksController::class, 'show'])->name('agent-tasks.show');
        Route::get('tasks/{agentTask}/edit', [AgentTasksController::class, 'edit'])->name('agent-tasks.edit');
        Route::put('tasks/{agentTask}', [AgentTasksController::class, 'update'])->name('agent-tasks.update');
        Route::delete('tasks/{agentTask}', [AgentTasksController::class, 'destroy'])->name('agent-tasks.destroy');
    });

// API routes for agent tools
Route::middleware(['auth'])->group(function () {
    Route::get('api/agent-tools', [AgentToolController::class, 'index'])->name('agent-tools.index');
    Route::post('api/agent-tools/execute', [AgentToolController::class, 'execute'])->name('agent-tools.execute');
});

Route::middleware(['auth'])->group(function () {
    Route::get('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
    Route::delete('invitations/{invitation}', [TeamInvitationController::class, 'decline'])->name('invitations.decline');
});

require __DIR__.'/settings.php';
