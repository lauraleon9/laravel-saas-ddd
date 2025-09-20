<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Solo para MySQL, SQLite no soporta triggers con la misma sintaxis
        if (DB::getDriverName() === 'mysql') {
            // BEFORE INSERT
            DB::unprepared("
                CREATE TRIGGER trg_subscriptions_bi
                BEFORE INSERT ON subscriptions
                FOR EACH ROW
                BEGIN
                    IF NEW.is_active = 1 AND EXISTS (
                        SELECT 1
                        FROM subscriptions
                        WHERE tenant_id = NEW.tenant_id
                          AND is_active = 1
                    ) THEN
                        SIGNAL SQLSTATE '45000'
                            SET MESSAGE_TEXT = 'Only one active subscription per tenant';
                    END IF;

                    -- coherencia temporal
                    IF NEW.ends_at IS NOT NULL AND NEW.ends_at <= NEW.starts_at THEN
                        SIGNAL SQLSTATE '45000'
                            SET MESSAGE_TEXT = 'ends_at must be null or greater than starts_at';
                    END IF;
                END
            ");

            // BEFORE UPDATE
            DB::unprepared("
                CREATE TRIGGER trg_subscriptions_bu
                BEFORE UPDATE ON subscriptions
                FOR EACH ROW
                BEGIN
                    -- Si se va a activar esta fila, verificar que no exista otra activa del mismo tenant
                    IF NEW.is_active = 1 AND (
                        NEW.is_active <> OLD.is_active OR NEW.tenant_id <> OLD.tenant_id
                    ) AND EXISTS (
                        SELECT 1
                        FROM subscriptions
                        WHERE tenant_id = NEW.tenant_id
                          AND is_active = 1
                          AND id <> NEW.id
                    ) THEN
                        SIGNAL SQLSTATE '45000'
                            SET MESSAGE_TEXT = 'Only one active subscription per tenant';
                    END IF;

                    -- coherencia temporal
                    IF NEW.ends_at IS NOT NULL AND NEW.ends_at <= NEW.starts_at THEN
                        SIGNAL SQLSTATE '45000'
                            SET MESSAGE_TEXT = 'ends_at must be null or greater than starts_at';
                    END IF;
                END
            ");
        }
    }

    public function down(): void
    {
        // Solo para MySQL
        if (DB::getDriverName() === 'mysql') {
            DB::unprepared("DROP TRIGGER IF EXISTS trg_subscriptions_bi");
            DB::unprepared("DROP TRIGGER IF EXISTS trg_subscriptions_bu");
        }
    }
};
