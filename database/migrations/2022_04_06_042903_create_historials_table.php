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
            $table->unsignedBigInteger('documento_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('accion_id');
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
