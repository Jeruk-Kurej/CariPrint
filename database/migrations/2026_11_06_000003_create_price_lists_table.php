<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->string('service');
            $table->string('paper_size')->nullable();
            $table->string('color_mode')->nullable();
            $table->unsignedInteger('price');
            $table->string('unit');
            $table->string('source')->default('estimated');
            $table->timestamps();

            $table->index(['shop_id', 'service']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_lists');
    }
};
