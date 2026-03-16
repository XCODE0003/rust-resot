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
        if (!Schema::hasColumn('promo_codes', 'public_uuid')) {
            Schema::table('promo_codes', function (Blueprint $table) {
                $table->string('public_uuid', 36)->nullable()->after('id');
            });
        }

        // Заполняем UUID для существующих записей
        DB::table('promo_codes')->whereNull('public_uuid')->cursor()->each(function ($promo) {
            DB::table('promo_codes')
                ->where('id', $promo->id)
                ->update(['public_uuid' => (string) Str::uuid()]);
        });

        // Добавляем уникальный индекс (если ещё нет)
        try {
            Schema::table('promo_codes', function (Blueprint $table) {
                $table->unique('public_uuid');
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // 1061 Duplicate key name — индекс уже есть, пропускаем
            if (strpos($e->getMessage(), '1061') === false) {
                throw $e;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('promo_codes', function (Blueprint $table) {
            $table->dropUnique(['public_uuid']);
            $table->dropColumn('public_uuid');
        });
    }
}
