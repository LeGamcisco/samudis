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
        Schema::create('error_logs', function (Blueprint $table) {
            $table->id();
            $table->smallInteger("error_id")->nullable();
            $table->smallInteger("type_id")->nullable(); // 1 Minutes, 5 Minutes, 1 Hour
            $table->smallInteger("parameter_id")->nullable();
            $table->smallInteger("status_id")->nullable();
            $table->double("measured")->nullable()->default(0);
            $table->double("corrective")->nullable()->default(0);
            $table->text("message")->nullable();
            $table->smallInteger("is_sent_ews")->default(0);
            $table->timestamp("sent_ews_at")->nullable()->default(null);
            $table->timestamp("time_group")->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('error_logs');
    }
};
