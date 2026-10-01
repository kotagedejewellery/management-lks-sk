<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('code', 30)->unique();
            $table->string('name', 50);
        });

        Schema::create('user_roles', function (Blueprint $table): void {
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('role_id')->constrained()->restrictOnDelete();
            $table->primary(['user_id', 'role_id']);
        });

        Schema::create('departments', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('code', 30)->unique();
            $table->string('name', 100);
            $table->boolean('is_active')->default(true);
            $table->timestampsTz();
        });

        Schema::create('teams', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code', 30)->unique();
            $table->string('name', 100);
            $table->boolean('is_active')->default(true);
            $table->timestampsTz();
        });

        Schema::create('santri_profiles', function (Blueprint $table): void {
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->primary('user_id');
            $table->string('gender', 10);
            $table->foreignUuid('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('team_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('leader_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('category', 100)->nullable();
            $table->string('level', 100)->nullable();
            $table->string('status', 15)->default('active');
            $table->timestampsTz();

            $table->index('leader_user_id');
            $table->index('department_id');
            $table->index('team_id');
        });

        DB::statement("ALTER TABLE santri_profiles ADD CONSTRAINT santri_profiles_gender_check CHECK (gender IN ('ikhwan', 'akhwat'))");
        DB::statement("ALTER TABLE santri_profiles ADD CONSTRAINT santri_profiles_status_check CHECK (status IN ('active', 'inactive'))");
        DB::statement('ALTER TABLE santri_profiles ADD CONSTRAINT santri_profiles_leader_not_self_check CHECK (leader_user_id IS NULL OR leader_user_id <> user_id)');
    }

    public function down(): void
    {
        Schema::dropIfExists('santri_profiles');
        Schema::dropIfExists('teams');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('roles');
    }
};
