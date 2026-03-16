<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class IncreasePlayersDataColumnSize extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE server_online_data MODIFY players_data MEDIUMTEXT NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE server_online_data MODIFY players_data TEXT NULL');
    }
}
