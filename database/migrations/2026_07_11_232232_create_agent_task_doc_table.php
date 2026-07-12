<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_task_doc', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agent_doc_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['agent_task_id', 'agent_doc_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_task_doc');
    }
};