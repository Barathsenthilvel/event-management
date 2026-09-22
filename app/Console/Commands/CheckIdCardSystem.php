<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\MemberIdCardService;
use Illuminate\Console\Command;

class CheckIdCardSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gnat:idcard-check 
                            {--user= : User ID to test generation for} 
                            {--generate : Generate cards for all active/approved members} 
                            {--force : Force regenerate existing cards}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnose and generate member ID cards (checks Python, Pillow, qrcode, templates, storage link, permissions)';

    /**
     * Execute the console command.
     */
    public function handle(MemberIdCardService $cardService): int
    {
        $this->info("=================================================");
        $this->info("       GNAT Member ID Card System Diagnostic     ");
        $this->info("=================================================");
        $this->newLine();

        $allOk = true;

        // 1. Python binary detection
        $python = $cardService->resolvePythonBinary();
        if ($python) {
            $this->line(" <info>✔</info> Python Binary: <comment>{$python}</comment>");
        } else {
            $allOk = false;
            $this->line(" <error>✘</error> Python Binary: <error>NOT FOUND</error>");
            $this->line("   -> Solution: Install python3 on server: <comment>sudo apt install -y python3</comment>");
            $this->line("   -> Or set custom path in .env: <comment>PYTHON_BINARY=/usr/bin/python3</comment>");
        }

        // 2. Python packages (Pillow, qrcode)
        if ($python) {
            $test = $cardService->testPythonEnvironment();
            if ($test['ok']) {
                $this->line(" <info>✔</info> Python Packages: <comment>Pillow and qrcode are operational</comment>");
            } else {
                $allOk = false;
                $this->line(" <error>✘</error> Python Packages: <error>{$test['error']}</error>");
                $this->line("   -> Solution: Run: <comment>pip3 install Pillow qrcode --break-system-packages</comment>");
                if (!empty($test['detail'])) {
                    $this->line("      Detail: " . trim($test['detail']));
                }
            }
        }

        // 3. Storage link
        $storageLinked = file_exists(public_path('storage'));
        if ($storageLinked) {
            $this->line(" <info>✔</info> Storage Link: <comment>public/storage symlink exists</comment>");
        } else {
            $this->line(" <comment>!</comment> Storage Link: <comment>public/storage does not exist</comment>");
            $this->line("   -> Solution: Run: <comment>php artisan storage:link</comment>");
        }

        // 4. Template files
        $frontTpl = public_path('images/id-card/template_front.png');
        $backTpl = public_path('images/id-card/template_back.png');
        if (file_exists($frontTpl) && file_exists($backTpl)) {
            $this->line(" <info>✔</info> Templates: <comment>Found template_front.png & template_back.png</comment>");
        } else {
            $allOk = false;
            $this->line(" <error>✘</error> Templates: <error>Missing template_front.png or template_back.png in public/images/id-card/</error>");
        }

        // 5. Storage Directory Writable
        $storageDir = storage_path('app/public/id-cards');
        if (!file_exists($storageDir)) {
            @mkdir($storageDir, 0775, true);
        }
        if (is_writable(storage_path('app/public'))) {
            $this->line(" <info>✔</info> Permissions: <comment>storage/app/public is writable</comment>");
        } else {
            $allOk = false;
            $this->line(" <error>✘</error> Permissions: <error>storage/app/public is not writable</error>");
            $this->line("   -> Solution: Run: <comment>chmod -R 775 storage</comment>");
        }

        $this->newLine();

        // 6. Test generation if requested
        $userId = $this->option('user');
        $generateAll = $this->option('generate');
        $force = (bool) $this->option('force');

        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found in database.");
                return 1;
            }
            $this->info("Generating ID card for User #{$user->id} ({$user->name})...");
            $res = $cardService->ensureCardGenerated($user, force: $force);
            if ($res && $cardService->cardsExist($user)) {
                $this->info("✔ ID card successfully generated for User #{$user->id}!");
                $this->line("  Front: " . $res['front']);
                $this->line("  Back:  " . $res['back']);
                $this->line("  URLs:  " . json_encode($cardService->getCardUrls($user), JSON_PRETTY_PRINT));
            } else {
                $this->error("✘ Failed to generate ID card for User #{$user->id}. Check storage/logs/laravel.log for details.");
            }
        } elseif ($generateAll) {
            $users = User::all();
            $this->info("Generating ID cards for {$users->count()} members...");
            $bar = $this->output->createProgressBar($users->count());
            $bar->start();
            $success = 0;
            foreach ($users as $u) {
                $res = $cardService->ensureCardGenerated($u, force: $force);
                if ($res && $cardService->cardsExist($u)) {
                    $success++;
                }
                $bar->advance();
            }
            $bar->finish();
            $this->newLine();
            $this->info("Completed: {$success}/{$users->count()} ID cards generated.");
        } else {
            $this->line("Tip: Run <comment>php artisan gnat:idcard-check --user=<ID></comment> to generate for a specific member.");
            $this->line("     Run <comment>php artisan gnat:idcard-check --generate</comment> to generate for all members.");
        }

        $this->newLine();
        return $allOk ? 0 : 1;
    }
}
