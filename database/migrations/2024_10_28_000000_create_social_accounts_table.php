<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('provider');
            $table->string('provider_user_id', 191);
            $table->text('token');
            $table->text('refresh_token')->nullable();
            $table->integer('expires_in')->nullable();
            $table->string('nickname')->nullable();
            $table->string('avatar')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'provider_user_id']);
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('social_accounts');
    }
};
