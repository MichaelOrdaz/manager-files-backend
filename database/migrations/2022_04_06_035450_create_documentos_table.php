<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->text('descripcion')->nullable();
            $table->string('ubicacion', 255)->nullable();
            $table->unsignedBigInteger('tipo_id')->comment('tipo de documento');
            $table->unsignedBigInteger('antecesor_id')->nullable()->comment('Si el documento esta dentro de otra carpeta aqui se sabra cual es su carpeta contenedora');
            $table->unsignedBigInteger('creador_id')->comment('usuario que creo el documento');
            $table->unsignedBigInteger('departamento_id')->comment('departamento al que pertence el documento');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tipo_id')->references('id')->on('tipos_de_documentos');
            $table->foreign('antecesor_id')->references('id')->on('documentos');
            $table->foreign('creador_id')->references('id')->on('users');
            $table->foreign('departamento_id')->references('id')->on('departamentos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documentos');
    }
}
