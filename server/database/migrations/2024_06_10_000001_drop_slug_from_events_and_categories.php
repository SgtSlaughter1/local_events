<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('events', 'slug')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('slug');
            });
        }
        if (Schema::hasColumn('categories', 'slug')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('slug');
            });
        }
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable();
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable();
        });
    }
};
