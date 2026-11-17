<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A visitor's request to add a print shop. It holds no user id, IP address or coordinates,
     * and never becomes public data until an admin creates the shop.
     */
    public function up(): void
    {
        Schema::create('shop_suggestions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('maps_url', 500)->nullable();
            $table->text('note')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('status')->default('pending')->index();
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_suggestions');
    }
};
