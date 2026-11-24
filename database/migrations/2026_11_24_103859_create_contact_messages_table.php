<?php

use App\Enums\ContactStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category');
            $table->string('email');
            $table->text('message');
            $table->string('status')->default(ContactStatus::New->value)->index();
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });

        // Supabase exposes every public table through its Data API. This app never uses it, so block it for new tables too.
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE "public"."contact_messages" ENABLE ROW LEVEL SECURITY');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
