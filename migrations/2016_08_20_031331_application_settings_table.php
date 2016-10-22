<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateApplicationSettingsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('field_name');
            $table->text('description', 65535)->nullable();
            $table->string('field_type');
            $table->text('value', 65535)->nullable();
            $table->text('field_params', 65535)->nullable();
            $table->string('category')->nullable();
            $table->integer('application_id')->unsigned()->nullable()->index('fk_application_settings_applications1_idx');
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
        Schema::drop('application_settings');
    }

}
