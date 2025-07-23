<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('timesheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->integer('break_minutes')->default(0);
            $table->text('description');
            $table->string('attachment')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('submitted');
            $table->timestamps();
            
            $table->index(['user_id', 'date']);
            $table->index(['project_id', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('timesheets');
    }
};