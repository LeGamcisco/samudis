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
        Schema::create('stacks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 100)->nullable()->index('stacks_code');
            $table->string('sispek_code')->nullable()->index('stacks_sispek_code');
            $table->string('ews_code')->nullable();
            $table->float('height', 0, 0)->default(0)->index('stacks_height');
            $table->float('diameter', 0, 0)->default(0)->index('stacks_diameter');
            $table->float('flow', 0, 0)->default(0)->index('stacks_flow');
            $table->string('lon', 20)->nullable();
            $table->string('lat', 20)->nullable();
            $table->float('oxygen_reference', 0, 0)->default(0)->index('stacks_oxygen_reference');
            $table->timestamp('created_at')->nullable();
            $table->string('created_by', 100)->default('');
            $table->string('created_ip', 20)->default('');
            $table->timestamp('updated_at')->nullable();
            $table->string('updated_by', 100)->default('');
            $table->string('updated_ip', 20)->default('');
            $table->smallInteger('is_deleted')->default(0);
            $table->softDeletes();
            $table->string('deleted_by', 100)->default('');
            $table->string('deleted_ip', 20)->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stacks');
    }
};
