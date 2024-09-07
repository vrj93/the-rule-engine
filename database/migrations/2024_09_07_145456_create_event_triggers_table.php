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
        Schema::create('event_triggers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_trigger_id')->constrained()->cascadeOnDelete();
            $table->string('dependency');
            $table->string('dependencyLink');
            $table->json('licenses');
            $table->string('cve');
            $table->string('cveLink');
            $table->float('cvss2');
            $table->float('cvss3');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_triggers');
    }
};
