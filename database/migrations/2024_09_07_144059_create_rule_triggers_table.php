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
        Schema::create('rule_triggers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_scan_id')->constrained()->cascadeOnDelete();
            $table->json('ruleActions');
            $table->string('ruleLink');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rule_triggers');
    }
};
