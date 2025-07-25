<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {            
            $table->renameColumn('start_date', 'po_date');
            $table->renameColumn('end_date', 'due_date');
            $table->string('on_production_date')->nullable()->before('priority');
        });
    }
    
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->renameColumn('po_date', 'start_date');
            $table->renameColumn('due_date', 'end_date');
            $table->dropColumn('on_production_date');
        });
    }
};
