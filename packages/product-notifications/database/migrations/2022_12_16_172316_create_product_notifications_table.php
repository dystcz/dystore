<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Lunar\Base\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create($this->prefix.'product_notifications', function (Blueprint $table) {
            $table->id();
            $table->morphs('purchasable', 'purchasable_index');

            $table->string('email');

            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('sent_at')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists($this->prefix.'product_notifications');
    }
};
