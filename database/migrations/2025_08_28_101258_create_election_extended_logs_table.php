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
        Schema::create('election_extended_logs', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(Str::uuid());
            $table->foreignUuid('election_id')->constrained('elections')->onDelete('cascade');
            $table->dateTime('old_end_at');
            $table->dateTime('new_end_at');
            $table->text('reason')->nullable();
            $table->foreignUuid('extended_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('election_extended_logs');
    }
};