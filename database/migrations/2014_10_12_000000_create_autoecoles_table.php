<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutoecolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('autoecoles', function (Blueprint $table) {
            $table->id();
            $table->string('nom_agence');
            $table->string('code_agence');
            $table->string('email')->unique();
            $table->string('adresse');
            $table->string('num_tel');
            $table->string('matri_fisc');
            $table->string('password');
            $table->rememberToken();
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
        Schema::dropIfExists('autoecoles');
    }
}
