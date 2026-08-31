<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Database and media backup.
 *
 * Writes outside the web root and prunes anything older than the retention
 * window. A backup nobody has restored is a rumour, so the command prints the
 * exact restore line for whatever it just wrote.
 */
class BackupBlog extends Command
{
    protected $signature = 'blog:backup {--keep=14 : Days of backups to retain}';

    protected $description = 'Dump the database and archive uploaded media';

    public function handle(): int
    {
        $dir = storage_path('backups');
        @mkdir($dir, 0750, true);

        $stamp = now()->format('Y-m-d_His');
        $connection = config('database.default');

        if ($connection === 'mysql') {
            $db = config('database.connections.mysql');
            $file = "{$dir}/db-{$stamp}.sql.gz";

            // Credentials go in via the environment so they never appear in the
            // process list, where any other account on the box could read them.
            $command = sprintf(
                'MYSQL_PWD=%s mysqldump --user=%s --host=%s --single-transaction --quick --no-tablespaces %s | gzip > %s',
                escapeshellarg((string) $db['password']),
                escapeshellarg((string) $db['username']),
                escapeshellarg((string) $db['host']),
                escapeshellarg((string) $db['database']),
                escapeshellarg($file),
            );

            exec($command, $out, $status);

            if ($status !== 0) {
                $this->error('Database dump failed.');

                return self::FAILURE;
            }

            $this->info("Database: {$file}");
            $this->line('  Restore: gunzip -c '.basename($file).' | mysql -u USER -p DATABASE');
        } else {
            $source = database_path('database.sqlite');

            if (is_file($source)) {
                copy($source, $file = "{$dir}/db-{$stamp}.sqlite");
                $this->info("Database: {$file}");
            }
        }

        $media = storage_path('app/media');

        if (is_dir($media)) {
            $archive = "{$dir}/media-{$stamp}.tar.gz";
            exec(sprintf('tar -czf %s -C %s .', escapeshellarg($archive), escapeshellarg($media)));
            $this->info("Media: {$archive}");
        }

        $this->prune($dir, (int) $this->option('keep'));

        return self::SUCCESS;
    }

    private function prune(string $dir, int $keepDays): void
    {
        $cutoff = now()->subDays($keepDays)->getTimestamp();
        $removed = 0;

        foreach (glob($dir.'/*') ?: [] as $path) {
            if (filemtime($path) < $cutoff) {
                unlink($path);
                $removed++;
            }
        }

        if ($removed) {
            $this->line("Pruned {$removed} backup(s) older than {$keepDays} days.");
        }
    }
}
