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

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');

            $table->tinyInteger('rating')->unsigned();
            $table->text('comment')->nullable();

            $table->dateTime('published_at')->nullable();

            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists($this->prefix.'reviews');
    }
};
