<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            // Quitar la unicidad global creada por la migración base
            $table->dropUnique('users_email_unique');

            // FK a tenants (BIGINT UNSIGNED para coincidir con $table->id())
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->nullOnDelete();

            // Campos adicionales para usuarios de empresa
            $table->string('role')->default('user')->after('password'); // admin, user, etc.
            $table->boolean('is_active')->default(true)->after('role');

            // Unicidad por tenant: el mismo email puede existir en tenants distintos
            $table->unique(['tenant_id', 'email'], 'users_tenant_email_unique');
            
            // Índice para consultas por tenant y estado
            $table->index(['tenant_id', 'is_active']);
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'is_active']);
            $table->dropUnique('users_tenant_email_unique');
            $table->dropColumn(['role', 'is_active']);
            $table->dropConstrainedForeignId('tenant_id');
            // Restaurar unicidad global de email
            $table->unique('email', 'users_email_unique');
        });
    }
};
