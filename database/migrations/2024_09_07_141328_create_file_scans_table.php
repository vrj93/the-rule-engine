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
        Schema::create('file_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ci_upload_id')->constrained()->cascadeOnDelete();
            $table->float('progress');
            $table->integer('vulnerabilitiesFound');
            $table->integer('unaffectedVulnerabilitiesFound');
            $table->string('automationsAction');
            $table->string('policyEngineAction');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_scans');
    }
};
