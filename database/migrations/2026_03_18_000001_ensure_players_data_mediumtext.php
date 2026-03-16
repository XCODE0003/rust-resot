<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Гарантирует, что players_data имеет тип MEDIUMTEXT.
 * Выполняет ALTER даже если колонка уже изменена — MySQL примет это без ошибки.
 */
class EnsurePlayersDataMediumtext extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE server_online_data MODIFY players_data MEDIUMTEXT NULL');
    }

    public function down()
    {
        DB::table('server_online_data')->update(['players_data' => null]);
        DB::statement('ALTER TABLE server_online_data MODIFY players_data TEXT NULL');
    }
}
