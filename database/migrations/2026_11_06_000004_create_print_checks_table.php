<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('print_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shop_id')->nullable()->constrained()->nullOnDelete();
            $table->string('file_name');
            $table->unsignedInteger('page_count');
            $table->json('color_pages');
            $table->json('suggested_bw_pages');
            $table->unsignedInteger('estimated_savings')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_checks');
    }
};
