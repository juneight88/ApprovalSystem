<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 191)->unique();
            $table->string('password'); // No hashing
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->enum('role', ['Personnel', 'Non-teaching personnel', 'Admin', 'Head of Office', 'Subject Coordinator','super_admin'])->nullable();
            $table->enum('department', ['CCIS', 'CTE', 'CAS', 'CCJE', 'CBM', 'CTHM', 'ELEMENTARY', 'JHS', 'SHS'])->nullable();
            $table->boolean('setup_complete')->default(false);
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
