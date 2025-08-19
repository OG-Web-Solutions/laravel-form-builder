<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('og_form_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('og_form_id');
            $table->text('token');
            $table->string('ip', 45)->nullable(); // Better than text for IPs
            $table->timestamps();

            $table->foreign('og_form_id')
                  ->references('id')
                  ->on('og_forms')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('og_form_submissions');
    }
}
