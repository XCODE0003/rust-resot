<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDisableCategoryDiscountToShopSetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop_sets', function (Blueprint $table) {
            $table->boolean('disable_category_discount')->default(false)->after('discount_percent')->comment('Отключить скидку категории для этого сета');
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
            $table->dropColumn('disable_category_discount');
        });
    }
}
