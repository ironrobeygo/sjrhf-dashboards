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
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('constituent_id');
            $table->string('name')->nullable();
            $table->string('organization_name')->nullable();
            $table->string('key_indicator')->nullable();
            $table->string('solicitors')->nullable();
            $table->string('assigned_solicitor_type')->nullable();
            $table->string('prospect_status')->nullable();
            $table->string('proposal_status')->nullable();
            $table->string('proposal_name')->nullable();
            $table->string('fund')->nullable();
            $table->string('purpose')->nullable();
            $table->date('date_added')->nullable();
            $table->decimal('target_ask', 15, 2)->nullable();
            $table->date('date_asked')->nullable();
            $table->decimal('amount_expected', 15, 2)->nullable();
            $table->date('date_expected')->nullable();
            $table->decimal('amount_funded', 15, 2)->nullable();
            $table->date('date_closed')->nullable();
            $table->date('deadline')->nullable();
            $table->boolean('is_inactive')->default(false);
            $table->string('record_id');
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
