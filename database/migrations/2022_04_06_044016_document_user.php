<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DocumentUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id')->nullable()->comment('the document, which must be a folder');
            $table->unsignedBigInteger('user_id')->nullable()->comment('user to which it is relating (sharing the folder/document)');
            $table->unsignedBigInteger('granted_by')->nullable()->comment('user to which it is relating (creator of relationship)');
            $table->string('permission')->nullable()->comment('type of permission the user has with the document');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('document_id')->references('id')->on('documents');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('granted_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_user');
    }
}
