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
        Schema::create('actions', function (Blueprint $table) {
            $table->id(); // Optional: could use Action System Record ID directly
            $table->bigInteger('action_system_record_id')->unique();
            $table->string('action_category');
            $table->date('action_completed_on')->nullable();
            $table->string('action_solicitor_list')->nullable();
            $table->string('action_type')->nullable();
            $table->string('constituent_id');
            $table->string('name')->nullable();
            $table->string('record_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actions');
    }
};
