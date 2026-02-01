<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountPercentToShopSetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop_sets', function (Blueprint $table) {
            $table->unsignedTinyInteger('discount_percent')->default(0)->after('can_gift')->comment('Процент скидки на сет');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shop_sets', function (Blueprint $table) {
            $table->dropColumn('discount_percent');
        });
    }
}
