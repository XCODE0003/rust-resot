<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDonatesSteamIdIndex extends Migration
{
    public function up()
    {
        try {
            Schema::table('donates', function (Blueprint $table) {
                $table->index('steam_id', 'donates_steam_id_index');
            });
        } catch (\Illuminate\Database\QueryException $e) {
            if (strpos($e->getMessage(), '1061') === false) {
                throw $e;
            }
            // Индекс уже существует
        }
    }

    public function down()
    {
        try {
            Schema::table('donates', function (Blueprint $table) {
                $table->dropIndex('donates_steam_id_index');
            });
        } catch (\Illuminate\Database\QueryException $e) {
            if (strpos($e->getMessage(), '1091') === false) {
                throw $e;
            }
        }
    }
}
