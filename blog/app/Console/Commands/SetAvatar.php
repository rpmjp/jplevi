<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\AvatarImport;
use Illuminate\Console\Command;

/**
 * Puts a photograph on an account from the command line.
 *
 * Uploaded media is deliberately not in the repository, so a photograph set on
 * one machine does not travel to the server with a deploy. This is the way to
 * put one there without going through the browser.
 *
 *   php artisan avatar:set you@example.com
 *   php artisan avatar:set you@example.com /path/to/photo.jpg
 */
class SetAvatar extends Command
{
    protected $signature = 'avatar:set {email : Whose account} {file? : The photograph, defaulting to the portrait in the site repository}';

    protected $description = "Set a person's profile photograph from a file";

    public function handle(AvatarImport $import): int
    {
        $user = User::firstWhere('email', $this->argument('email'));

        if (! $user) {
            $this->error("No account with the address {$this->argument('email')}.");

            return self::FAILURE;
        }

        $file = $this->argument('file') ?: base_path('../public/robert-jean-pierre.png');

        try {
            $path = $import->forUser($user, $file);
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info("Set {$user->name}'s photograph.");
        $this->line('  stored: '.$path);
        $this->line('  shown:  '.$user->avatarUrl(96));

        return self::SUCCESS;
    }
}
