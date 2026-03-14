<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['super_admin', 'admin', 'editor'])->default('editor')->after('password');
            $table->string('avatar')->nullable()->after('role');
            $table->text('bio')->nullable()->after('avatar');
            $table->boolean('is_active')->default(true)->after('bio');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'avatar', 'bio', 'is_active']);
        });
    }
};
