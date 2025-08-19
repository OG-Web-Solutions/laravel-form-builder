<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOgFormSubmissionValueLargeValuesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('og_form_submission_large_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('og_value_id');
            $table->longText('value')->nullable();
            $table->timestamps();

            $table->foreign('og_value_id')
                ->references('id')
                ->on('og_form_submission_values')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('og_form_submission_large_values');
    }
}
