<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_docs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->text('content')->nullable();
            $table->string('version')->default('1.0');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['team_id', 'slug']);
            $table->index('team_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_docs');
    }
};