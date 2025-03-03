<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEducationLevelColumnLength extends Migration
{
    public function up()
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->string('education_level', 50)->change();
        });
    }

    public function down()
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->string('education_level', 20)->change();
        });
    }
}
