<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            // Add new address fields
            $table->string('street_address')->nullable()->after('location');
            $table->string('city')->nullable()->after('street_address');
            $table->string('country')->nullable()->after('city');
        });
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'street_address',
                'city',
                'country'
            ]);
        });
    }
};
