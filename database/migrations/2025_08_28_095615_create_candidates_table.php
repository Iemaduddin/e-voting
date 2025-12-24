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
        Schema::create('candidates', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(Str::uuid());
            $table->foreignUuid('election_id')->constrained('elections')->onDelete('cascade');
            $table->foreignUuid('ketua_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('wakil_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->text('visi');
            $table->text('misi');
            $table->text('cv');
            $table->text('photo');
            $table->string('link')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
