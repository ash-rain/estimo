<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateTenantCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create
                            {name : The company name}
                            {email : The owner email}
                            {--password= : The owner password (optional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new tenant with an owner user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->option('password') ?? 'password';

        try {
            // Create tenant
            $tenant = Tenant::create([
                'name' => $name,
                'email' => $email,
                'plan' => 'starter',
                'trial_ends_at' => now()->addDays(14),
            ]);

            $this->info("Tenant created: {$tenant->id}");

            // Create domain
            $subdomain = Str::slug($name);
            $domain = $tenant->domains()->create([
                'domain' => $subdomain . '.' . config('app.domain', 'estimo.test'),
            ]);

            $this->info("Domain created: {$domain->domain}");

            // Initialize tenancy and create owner user
            tenancy()->initialize($tenant);

            $user = User::create([
                'name' => explode('@', $email)[0],
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'owner',
                'email_verified_at' => now(),
            ]);

            $this->info("Owner user created: {$user->email}");
            $this->info("Password: {$password}");

            $this->newLine();
            $this->info("✅ Tenant setup complete!");
            $this->info("Access at: http://{$domain->domain}");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to create tenant: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}
