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
        Schema::create('image_to_process', function (Blueprint $table) {
            $table->id('image_to_process_id')->autoIncrement();
            $table->string('path', 255)->nullable();
            $table->string('upload_to', 255)->nullable()->comment('customize image storage, else default in same server');
            $table->bigInteger('property_address_image_id')->default(0);

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });

        Schema::table('image_to_process', function (Blueprint $table) {
            $table->foreignId('resolution_type_id')->references('resolution_type_id')->on('resolution_type')->onDelete('cascade');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('image_to_process');
    }
};
