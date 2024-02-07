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
        Schema::create('glossary_subjects', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('en', 100)->nullable()->default('');
            $table->string('fa', 100)->nullable()->default('');
            $table->string('pa', 100)->nullable()->default('');
            $table->string('mj', 100)->nullable()->default('');
            $table->string('no', 100)->nullable()->default('');
            $table->string('ps', 100)->nullable()->default('');
            $table->string('sh', 100)->nullable()->default('');
            $table->string('sw', 100)->nullable()->default('');
            $table->string('uz', 100)->nullable()->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('glossary_subjects');
    }
};
