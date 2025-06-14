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
        Schema::table('job_applications', function (Blueprint $table) {
            // First drop the existing foreign key constraint
            $table->dropForeign(['post_job_id']);
            
            // Rename the column
            $table->renameColumn('post_job_id', 'job_id');
        });

        // Add the new foreign key constraint
        Schema::table('job_applications', function (Blueprint $table) {
            $table->foreign('job_id')->references('id')->on('post_jobs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['job_id']);
            
            // Rename the column back
            $table->renameColumn('job_id', 'post_job_id');
        });

        // Add back the original foreign key constraint
        Schema::table('job_applications', function (Blueprint $table) {
            $table->foreign('post_job_id')->references('id')->on('post_jobs')->onDelete('cascade');
        });
    }
};
