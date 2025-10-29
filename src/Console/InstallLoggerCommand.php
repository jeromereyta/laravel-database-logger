<?php

declare(strict_types=1);

namespace Jeromereyta\DatabaseLogger\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InstallLoggerCommand extends Command
{
    protected $signature = 'database-logger:install';
    protected $description = 'Generate a migration for the database logger table based on your configuration.';

    public function handle()
    {
        $filesystem = new Filesystem;
        $timestamp = now()->format('Y_m_d_His');
        $table = config('database-logger.table_name', 'logs');
        $path = database_path("migrations/{$timestamp}_create_{$table}_table.php");

        $filesystem->put($path, $this->generateMigration($table));

        $this->info("✅ Migration created: {$path}");
    }

    protected function generateMigration(string $table): string
    {
        $customColumns = config('database-logger.custom_columns', []);

        $columns = [
            "\$table->id();",
            "\$table->string('level');",
            "\$table->text('message');",
            "\$table->json('context')->nullable();",
            "\$table->foreignId('user_id')->nullable();",
            "\$table->timestamps();",
        ];

        foreach ($customColumns as $name => $definition) {
            [$type, $nullable] = array_pad(explode('|', $definition), 2, null);
            $line = "\$table->{$type}('{$name}')";
            if ($nullable === 'nullable') $line .= "->nullable()";
            $columns[] = $line . ';';
        }

        $columnsStr = implode("\n            ", $columns);

        return <<<PHP
<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{$table}', function (Blueprint \$table) {
            {$columnsStr}
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{$table}');
    }
};
PHP;
    }
}