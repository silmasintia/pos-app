<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('profile_name');
            $table->string('alias')->nullable();
            $table->string('identity_number')->nullable();
            $table->text('address')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('description_1')->nullable();
            $table->text('description_2')->nullable();
            $table->text('description_3')->nullable();
            $table->string('logo')->nullable();
            $table->string('logo_dark')->nullable();
            $table->string('favicon')->nullable();
            $table->string('banner')->nullable();
            $table->string('login_background')->nullable();
            $table->string('theme')->nullable();
            $table->string('theme_color')->nullable();
            $table->string('boxed_layout')->nullable();
            $table->string('sidebar_type')->nullable();
            $table->string('card_border')->nullable();
            $table->string('direction')->nullable();
            $table->text('embed_youtube')->nullable();
            $table->text('embed_map')->nullable();
            $table->string('keyword')->nullable();
            $table->text('keyword_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
