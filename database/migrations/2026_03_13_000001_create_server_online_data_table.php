<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServerOnlineDataTable extends Migration
{
    public function up()
    {
        Schema::create('server_online_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('server_id')->index();
            $table->integer('online_count')->default(0);
            $table->integer('online_max')->default(0);
            $table->integer('online_queued')->default(0);
            $table->text('players_data')->nullable();
            $table->timestamp('updated_at');
            
            $table->unique('server_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('server_online_data');
    }
}
