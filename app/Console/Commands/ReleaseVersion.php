<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReleaseVersion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'release:new(version)';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new deployable release';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $version = $this->argument('version');

        $commit = trim(shell_exec('git rev-parse  --short HEAD'));

        DB::table('app_version')->insert([
            'version' => $version,
            'commit_hash' => $commit,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Here you can add logic to create a new release version,
        // such as updating a database record, creating a tag in version control, etc.

        $this->info("New release version {$version} has been created. (Commit: {$commit})");
    }
}
