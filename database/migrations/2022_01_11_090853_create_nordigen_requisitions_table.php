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
        Schema::create('nordigen_requisitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('institution_id');
            $table->uuid('agreement');
            $table->uuid('reference');
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
        Schema::dropIfExists('nordigen_requisitions');
    }
};
