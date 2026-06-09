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
        Schema::create('shared_templates', function (Blueprint $table) {
            $table->id();
            $table->string('code', 256)->unique();
            $table->foreignId('template_id')->constrained()->cascadeOnDelete();
            $table->json('snapshot');
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shared_templates');
    }
};
