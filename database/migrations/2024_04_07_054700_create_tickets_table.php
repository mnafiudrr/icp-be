<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->string('id', 6)->primary()->unique()->index();
            $table->string('title');
            $table->enum('type', ['task', 'bug']);
            $table->string('assigned_to', 6);
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('cascade');
            $table->text('description');
            $table->enum('label', ['To Do', 'Doing']);
            $table->string('project_id', 6);
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->string('created_by', 6);
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
