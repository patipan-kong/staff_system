<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('role')->default(0); // 0=Staff, 1=Leader, 2=Manager, 3=Test, 99=Admin
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->string('position');
            $table->decimal('salary', 10, 2)->nullable();
            $table->string('photo')->nullable();
            $table->date('hire_date');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};