<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            // Crear primero las columnas sin foreign keys
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('plan_id');

            // período de vigencia (fila = un período)
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true)->index();

            // snapshots para conservar histórico aunque cambie el plan luego
            $table->decimal('price_at_signup', 10, 2);
            $table->unsignedInteger('user_limit_snapshot')->nullable();
            $table->json('features_snapshot')->nullable();

            $table->timestamps();

            // Indexes primero
            $table->index('tenant_id');
            $table->index('plan_id');
            // Índice compuesto para optimizar consultas de suscripciones activas por tenant
            $table->index(['tenant_id', 'is_active'], 'idx_tenant_active');
        });

        // Agregar foreign keys después
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('restrict');
        });

        // validación adicional (CHECK) para coherencia temporal
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE subscriptions
                ADD CONSTRAINT chk_subscriptions_period
                CHECK (ends_at IS NULL OR ends_at > starts_at)
            ");
        }
    }

    public function down(): void {
        Schema::dropIfExists('subscriptions');
    }
};
