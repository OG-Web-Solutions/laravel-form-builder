<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOgFormSubmissionValuesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('og_form_submission_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('og_form_submission_id');
            $table->string('label')->nullable();
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->foreign('og_form_submission_id')
                ->references('id')
                ->on('og_form_submissions')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('og_form_submission_values');
    }
}
