<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Illuminate\Support\Str;

class DeployController extends Controller
{
    protected string $basePath = 'C:\\inetpub\\wwwroot\\sales-and-inventory-software';
    protected string $branch   = 'db-sync-v2';

    public function deploy(Request $request)
    {
        // 1️⃣ Security
        if ($request->header('X-DEPLOY-TOKEN') !== config('deploy.token')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // 2️⃣ Version (timestamp-based, safe)
        $version = now()->format('Ymd_His');
        $releasePath = "{$this->basePath}\\releases\\{$version}";
        $sharedPath  = "{$this->basePath}\\shared";
        $currentPath = "{$this->basePath}\\current";

        $commands = [

            // Create release directory
            "mkdir {$releasePath}",

            // Clone repository
            "git clone -b {$this->branch} https://github.com/SunDimension/sales-and-inventory-software.git {$releasePath}",

            // Link .env
            "cmd /c mklink {$releasePath}\\.env {$sharedPath}\\.env",

            // Link storage
            "cmd /c mklink /J {$releasePath}\\storage {$sharedPath}\\storage",

            // Laravel optimizations
            // "cd {$releasePath} && php artisan migrate --force",
            "cd {$releasePath} && composer install --no-dev --optimize-autoloader",
            "cd {$releasePath} && php artisan key:generate",
            "cd {$releasePath} && php artisan migrate --force",
            "cd {$releasePath} && php artisan optimize",

            // Switch live version (atomic)
            "if exist {$currentPath} ren {$currentPath} current_old",
            "cmd /c mklink /J {$currentPath} {$releasePath}",
        ];

        $output = [];

        foreach ($commands as $command) {
            $process = Process::fromShellCommandline($command);
            $process->setTimeout(600);
            $process->run();

            $output[] = [
                'command' => $command,
                'output'  => trim($process->getOutput() . $process->getErrorOutput()),
            ];

            if (! $process->isSuccessful()) {
                return response()->json([
                    'status' => 'failed',
                    'step'   => $command,
                    'logs'   => $output,
                ], 500);
            }
        }

        return response()->json([
            'status'  => 'success',
            'version' => $version,
            'logs'    => $output,
        ]);
    }

    /**
     * 🔁 Rollback to previous release
     */
    public function rollback(Request $request)
    {
        if ($request->header('X-DEPLOY-TOKEN') !== config('deploy.token')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $targetVersion = $request->input('version');

        if (! $targetVersion) {
            return response()->json(['error' => 'Version required'], 422);
        }

        $releasePath = "{$this->basePath}\\releases\\{$targetVersion}";
        $currentPath = "{$this->basePath}\\current";

        if (! is_dir($releasePath)) {
            return response()->json(['error' => 'Release not found'], 404);
        }

        $commands = [
            "if exist {$currentPath} rmdir {$currentPath}",
            "cmd /c mklink /J {$currentPath} {$releasePath}",
        ];

        foreach ($commands as $command) {
            $process = Process::fromShellCommandline($command);
            $process->run();

            if (! $process->isSuccessful()) {
                return response()->json(['error' => 'Rollback failed'], 500);
            }
        }

        return response()->json([
            'status'  => 'rolled back',
            'version' => $targetVersion,
        ]);
    }
}
