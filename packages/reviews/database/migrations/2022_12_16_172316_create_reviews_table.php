<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Lunar\Base\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create($this->prefix.'reviews', function (Blueprint $table) {
            $table->id();

            $table->morphs('purchasable');
            $table->userForeignKey();

            $table->tinyInteger('rating')->unsigned();
            $table->text('comment')->nullable();

            $table->dateTime('published_at')->useCurrent()->nullable();

            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists($this->prefix.'reviews');
    }
};
