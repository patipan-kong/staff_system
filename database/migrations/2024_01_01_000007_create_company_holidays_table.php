<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('company_holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('date');
            $table->text('description')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->timestamps();
            
            $table->index('date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_holidays');
    }
};