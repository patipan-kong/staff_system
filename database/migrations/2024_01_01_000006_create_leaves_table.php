<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['vacation', 'sick', 'sick_with_certificate', 'personal']);
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('days', 4, 1);
            $table->text('reason');
            $table->string('medical_certificate')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'start_date']);
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('leaves');
    }
};