<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('project_assigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('role')->nullable();
            $table->date('assigned_date');
            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'project_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_assigns');
    }
};