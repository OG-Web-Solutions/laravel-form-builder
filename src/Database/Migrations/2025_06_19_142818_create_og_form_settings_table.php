<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOgFormSettingsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('og_form_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('og_form_id')->unique();

            // Admin Email Settings
            $table->boolean('admin_email_enabled')->default(false);
            $table->string('admin_email_subject')->nullable();
            $table->text('admin_email_body')->nullable();
            $table->json('admin_emails')->nullable();

            // Customer Email Settings
            $table->boolean('customer_email_enabled')->default(false);
            $table->string('customer_email_subject')->nullable();
            $table->text('customer_email_body')->nullable();
            $table->json('customer_emails')->nullable();

            // Post-submission settings
            $table->text('success_message')->nullable();
            $table->text('failure_message')->nullable();
            $table->string('redirect_url')->nullable();

            // Attach Submission as CSV
            $table->boolean('admin_csv_enabled')->default(true);

            $table->timestamps();

            $table->foreign('og_form_id', 'fk_form_settings_form_id')
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
        Schema::dropIfExists('og_form_settings');
    }
}
