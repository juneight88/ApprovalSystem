<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('document_requests', function (Blueprint $table) {
            $table->id();
            $table->date('date_request');
            $table->string('type_of_document');
            $table->string('other_document')->nullable();
            $table->date('date_required');
            $table->string('paper_size');
            $table->string('mode');
            $table->integer('number_of_pages');
            $table->integer('number_of_copies');
            $table->string('file')->nullable(); // Stores the file path
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_requests');
    }
}

