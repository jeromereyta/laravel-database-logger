<?php

declare(strict_types=1);

namespace Jeromereyta\DatabaseLogger;

use Illuminate\Support\Facades\DB;

class DatabaseLogger
{
    public static function log(string $level, string $message, array $context = [], ?int $userId = null): void
    {
        $table = config('database-logger.table_name', 'logs');

        DB::table($table)->insert([
            'level' => $level,
            'message' => $message,
            'context' => json_encode($context),
            'user_id' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}