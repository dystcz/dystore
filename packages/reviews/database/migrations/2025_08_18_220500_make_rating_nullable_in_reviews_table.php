<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Lunar\Base\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table($this->prefix.'reviews', function (Blueprint $table) {
            $table->tinyInteger('rating')->unsigned()->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table($this->prefix.'reviews', function (Blueprint $table) {
            $table->tinyInteger('rating')->unsigned()->nullable(false)->change();
        });
    }
};
