<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('nom');
            $table->string('prenom');
            $table->date('date_naissance');
            $table->string('cin')->unique();
            $table->integer('num_tel');
            $table->string('email')->unique();
            $table->string('adresse');
            $table->string('prix_heure_code');
            $table->string('prix_heure');
            $table->string('prix_heure_park');
            $table->string('avance');
            $table->integer('nbr_heure_total_code');
            $table->integer('nbr_heure_total');
            $table->integer('nbr_heure_total_park');
            $table->string('password');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    

    public function down()
    {
        Schema::dropIfExists('candidats');
    }
}
