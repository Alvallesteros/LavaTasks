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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')->nullable()->constrained();

            $table->integer('priority');
            $table->string('title');
            $table->text('description');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function(Blueprint Stable) {
            if (Schema::hasColumn('tasks', 'project_id')) {
                $table->dropForeign(['project_id'])
            }
        });

        Schema::dropIfExists('tasks');
    }
};
