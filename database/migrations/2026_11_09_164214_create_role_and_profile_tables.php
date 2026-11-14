<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 2 migrations:
 * - Add `role` to users
 * - profile, experiences, skill_categories, skills
 * - project_images, technologies, project_technology
 * - services, invitations, contact_messages, activity_log
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Role column on users
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('admin')->after('email'); // 'admin' | 'superadmin'
        });

        // 2. Single-row site profile
        Schema::create('profile', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title');
            $table->text('bio')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('resume_path')->nullable();
            $table->string('email')->nullable();
            $table->string('github_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('location')->nullable();
            $table->string('tagline')->nullable(); // one-line pitch for hero
            $table->timestamps();
        });

        // 3. Work / education / certification timeline
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['work', 'education', 'certification']);
            $table->string('organization');
            $table->string('role');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // 4. Skill categories
        Schema::create('skill_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // 5. Skills
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skill_category_id')->constrained('skill_categories')->cascadeOnDelete();
            $table->string('name');
            $table->unsignedTinyInteger('level')->nullable(); // 1–5, optional
            $table->string('icon')->nullable(); // lucide icon name or SVG path
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // 6. Project images (screenshots)
        Schema::create('project_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('caption')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // 7. Technologies (for project filters)
        Schema::create('technologies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // 8. Project ↔ Technology pivot
        Schema::create('project_technology', function (Blueprint $table) {
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('technology_id')->constrained()->cascadeOnDelete();
            $table->primary(['project_id', 'technology_id']);
        });

        // 9. Services ("What I Can Build" cards)
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('icon')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // 10. Admin invitations (invite-only registration)
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->string('code_hash')->unique();
            $table->enum('role', ['admin', 'superadmin'])->default('admin');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });

        // 11. Contact messages from visitors
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('subject')->nullable();
            $table->text('message');
            $table->string('ip_hash')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('handled_at')->nullable();
            $table->boolean('is_spam')->default(false);
            $table->timestamps();
        });

        // 12. Activity log
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action'); // created | updated | deleted
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('changes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn('role'));
        Schema::dropIfExists('activity_log');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('invitations');
        Schema::dropIfExists('services');
        Schema::dropIfExists('project_technology');
        Schema::dropIfExists('technologies');
        Schema::dropIfExists('project_images');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('skill_categories');
        Schema::dropIfExists('experiences');
        Schema::dropIfExists('profile');
    }
};
