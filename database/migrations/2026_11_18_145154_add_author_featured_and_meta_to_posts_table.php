<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->boolean('is_featured')->default(false)->after('status');
            $table->string('meta_description', 180)->nullable()->after('excerpt');
            $table->index(['status', 'is_featured']);
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['status', 'is_featured']);
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn(['is_featured', 'meta_description']);
        });
    }
};
