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
        Schema::create('site_has_photo', function (Blueprint $table) {
            $table->foreignId('site_id')->constrained('site')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('photo_id')->constrained('photo')->onDelete('no action')->onUpdate('no action');
            $table->timestamp('created_at')->useCurrent();
            $table->primary(['site_id', 'photo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_has_photo');
    }
};
