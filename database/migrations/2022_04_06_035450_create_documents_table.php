<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('location', 255)->nullable();
            $table->date('date')->nullable();
            $table->string('min_identifier')->nullable();
            $table->string('max_identifier')->nullable();
            $table->json('tags')->nullable();
            $table->unsignedBigInteger('type_id')->nullable()->comment('document_type belong to document');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('If the document is inside another folder, here it will be known which is its containing folder');
            $table->unsignedBigInteger('creator_id')->nullable()->comment('user that register document');
            $table->unsignedBigInteger('department_id')->nullable()->comment('department belongs to document');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('type_id')->references('id')->on('document_types');
            $table->foreign('parent_id')->references('id')->on('documents');
            $table->foreign('creator_id')->references('id')->on('users');
            $table->foreign('department_id')->references('id')->on('departments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documents');
    }
}
