<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AddPublicUuidToPromoCodesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('promo_codes', function (Blueprint $table) {
            $table->uuid('public_uuid')->nullable()->after('id');
        });

        // Заполняем UUID для существующих записей
        DB::table('promo_codes')->whereNull('public_uuid')->cursor()->each(function ($promo) {
            DB::table('promo_codes')
                ->where('id', $promo->id)
                ->update(['public_uuid' => (string) Str::uuid()]);
        });

        // Делаем поле NOT NULL и добавляем уникальный индекс
        Schema::table('promo_codes', function (Blueprint $table) {
            $table->uuid('public_uuid')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('promo_codes', function (Blueprint $table) {
            $table->dropColumn('public_uuid');
        });
    }
}
