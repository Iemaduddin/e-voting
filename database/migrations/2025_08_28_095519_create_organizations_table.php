<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(Str::uuid());
            $table->string('shorten_name', 100)->unique();
            $table->text('vision');
            $table->text('mision');
            $table->text('description');
            $table->json('link_media_social')->nullable();
            $table->bigInteger('whatsapp_number')->nullable();
            $table->uuid('user_id');
            $table->enum('organization_type', ['HMJ', 'LT', 'UKM']);
            $table->string('logo');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
