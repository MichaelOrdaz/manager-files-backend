<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistorialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historial', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('documento_id')->nullable()->comment('documento al cual se le lleva el seguimiento');
            $table->unsignedBigInteger('user_id')->nullable()->comment('usuario que realizo la accion sobre el documento');
            $table->unsignedBigInteger('accion_id')->nullable()->comment('el tipo de accion que realizo el usuario');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('documento_id')->references('id')->on('documentos');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('accion_id')->references('id')->on('acciones_historial');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historial');
    }
}
