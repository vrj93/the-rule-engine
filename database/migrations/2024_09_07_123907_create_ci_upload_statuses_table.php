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
        Schema::create('ci_uploads', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ciUploadId');
            $table->bigInteger('uploadProgramsFileId');
            $table->integer('totalScans');
            $table->integer('remainingScans');
            $table->string('percentage');
            $table->integer('estimatedDaysLeft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ci_uploads');
    }
};
