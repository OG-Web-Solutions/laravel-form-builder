<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuilderFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('og_forms', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Form name/title
            $table->json('fields'); // JSON field for storing form structure
            $table->enum('status', ['active', 'inactive'])->default('inactive'); // Form status
            $table->softDeletes();
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
        Schema::dropIfExists('og_forms');
    }
}
