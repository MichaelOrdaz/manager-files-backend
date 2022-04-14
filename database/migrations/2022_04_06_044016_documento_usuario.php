<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DocumentoUsuario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documento_usuario', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('documento_id')->nullable()->comment('el documento, que debe ser carpeta');
            $table->unsignedBigInteger('user_id')->nullable()->comment('usuario al cual se le esta relacionando (compartiendo la carpeta (documento))');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('documento_id')->references('id')->on('documentos');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documento_usuario');
    }
}
