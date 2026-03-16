<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToDonatesSteamId20260205 extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('donates', function (Blueprint $table) {
            $table->index('steam_id', 'donates_steam_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('donates', function (Blueprint $table) {
            $table->dropIndex('donates_steam_id_index');
        });
    }
}
