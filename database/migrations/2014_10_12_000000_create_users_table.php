<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('email_zoom')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('password_zoom')->nullable();
            $table->rememberToken();
            $table->string('firebase_uid')->nullable();
            $table->unsignedBigInteger('tutor_id')->nullable()->comment('Usuario que es tutor (Rol: Padre de familia) del usuario con rol alumno');
            $table->boolean('activo')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tutor_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
