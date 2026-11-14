<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Add full HLD columns to the already-created projects table. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('title')->after('id');
            $table->string('slug')->unique()->after('title');
            $table->string('summary')->nullable()->after('slug');
            $table->text('problem')->nullable()->after('summary');
            $table->text('solution')->nullable()->after('problem');
            $table->string('role')->nullable()->after('solution');
            $table->enum('status', ['draft', 'published'])->default('draft')->after('role');
            $table->string('cover_image')->nullable()->after('status');
            $table->string('demo_url')->nullable()->after('cover_image');
            $table->string('repo_url')->nullable()->after('demo_url');
            $table->boolean('is_featured')->default(false)->after('repo_url');
            $table->timestamp('published_at')->nullable()->after('is_featured');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'title', 'slug', 'summary', 'problem', 'solution',
                'role', 'status', 'cover_image', 'demo_url', 'repo_url',
                'is_featured', 'published_at',
            ]);
        });
    }
};
