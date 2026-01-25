# Estimo - Quick Start Guide

This guide will help you set up and start developing Estimo in the next few hours.

---

## Prerequisites

Before starting, ensure you have:
- PHP 8.2 or higher
- Composer
- Node.js 18+ and NPM
- MySQL 8.0+ or PostgreSQL 13+
- Redis
- Git

---

## Initial Setup (30-60 minutes)

### Step 1: Create Laravel Project

```bash
# Create new Laravel project
composer create-project laravel/laravel estimo

# Navigate to project
cd estimo

# Initialize git
git init
git add .
git commit -m "Initial Laravel setup"
```

### Step 2: Install Core Dependencies

```bash
# Install Livewire 4
composer require livewire/livewire

# Install multi-tenancy package
composer require stancl/tenancy

# Install development tools
composer require --dev laravel/pint
composer require --dev pestphp/pest
composer require --dev pestphp/pest-plugin-laravel
composer require --dev barryvdh/laravel-debugbar

# Install frontend dependencies
npm install -D tailwindcss @tailwindcss/forms @tailwindcss/typography
npm install alpinejs
```

### Step 3: Configure Tailwind CSS

```bash
# Initialize Tailwind
npx tailwindcss init -p
```

Edit `tailwind.config.js`:
```javascript
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./app/Livewire/**/*.php",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#f0f9ff',
          100: '#e0f2fe',
          200: '#bae6fd',
          300: '#7dd3fc',
          400: '#38bdf8',
          500: '#0ea5e9',
          600: '#0284c7',
          700: '#0369a1',
          800: '#075985',
          900: '#0c4a6e',
        },
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}
```

Edit `resources/css/app.css`:
```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

### Step 4: Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

Edit `.env` file:
```env
APP_NAME=Estimo
APP_URL=http://estimo.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=estimo
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Step 5: Set Up Database

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE estimo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Or for PostgreSQL
# createdb estimo
```

---

## Multi-Tenancy Setup (60-90 minutes)

### Step 1: Configure Tenancy Package

Publish tenancy configuration:
```bash
php artisan tenancy:install
```

Edit `config/tenancy.php` to customize tenant identification (subdomain vs path).

### Step 2: Create Tenant Model

```bash
php artisan make:model Tenant
```

Edit `app/Models/Tenant.php`:
```php
<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $fillable = [
        'id',
        'name',
        'email',
        'plan',
        'trial_ends_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
    ];
}
```

### Step 3: Run Tenancy Migrations

```bash
php artisan migrate
```

---

## Authentication Setup (30 minutes)

### Install Laravel Breeze with Livewire

```bash
composer require laravel/breeze --dev
php artisan breeze:install livewire
npm install
npm run build
php artisan migrate
```

### Customize for Multi-Tenancy

Create tenant registration controller and views to create workspace during signup.

---

## First Feature: Client Management (2-3 hours)

### Step 1: Create Migration

```bash
php artisan make:migration create_clients_table
```

Edit the migration:
```php
Schema::create('clients', function (Blueprint $table) {
    $table->id();
    $table->string('company_name');
    $table->string('contact_name')->nullable();
    $table->string('email')->nullable();
    $table->string('phone')->nullable();
    $table->text('address')->nullable();
    $table->string('city')->nullable();
    $table->string('state')->nullable();
    $table->string('postal_code')->nullable();
    $table->string('country')->default('US');
    $table->string('currency', 3)->default('USD');
    $table->boolean('tax_exempt')->default(false);
    $table->text('notes')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();
});
```

Run migration:
```bash
php artisan migrate
```

### Step 2: Create Model

```bash
php artisan make:model Client
```

Edit `app/Models/Client.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_name',
        'contact_name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'currency',
        'tax_exempt',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'tax_exempt' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }
}
```

### Step 3: Create Livewire Components

```bash
php artisan make:livewire Clients/ClientList
php artisan make:livewire Clients/ClientForm
```

Edit `app/Livewire/Clients/ClientList.php`:
```php
<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;

class ClientList extends Component
{
    use WithPagination;

    public $search = '';
    public $showForm = false;
    public $editingClientId = null;

    public function render()
    {
        return view('livewire.clients.client-list', [
            'clients' => Client::query()
                ->when($this->search, function ($query) {
                    $query->where('company_name', 'like', '%' . $this->search . '%')
                          ->orWhere('contact_name', 'like', '%' . $this->search . '%')
                          ->orWhere('email', 'like', '%' . $this->search . '%');
                })
                ->where('is_active', true)
                ->latest()
                ->paginate(15),
        ]);
    }

    public function createClient()
    {
        $this->showForm = true;
        $this->editingClientId = null;
    }

    public function editClient($clientId)
    {
        $this->showForm = true;
        $this->editingClientId = $clientId;
    }

    public function closeForm()
    {
        $this->showForm = false;
        $this->editingClientId = null;
    }
}
```

### Step 4: Create Views

Create `resources/views/livewire/clients/client-list.blade.php`:
```blade
<div>
    <div class="mb-6 flex justify-between items-center">
        <div class="flex-1 max-w-lg">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search clients..."
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
            >
        </div>

        <button
            wire:click="createClient"
            class="ml-4 px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700"
        >
            Add Client
        </button>
    </div>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Company</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($clients as $client)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $client->company_name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $client->contact_name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $client->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $client->phone }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button
                                wire:click="editClient({{ $client->id }})"
                                class="text-primary-600 hover:text-primary-900"
                            >
                                Edit
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No clients found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-4">
            {{ $clients->links() }}
        </div>
    </div>

    @if($showForm)
        <livewire:clients.client-form
            :client-id="$editingClientId"
            @saved="closeForm"
            @cancelled="closeForm"
        />
    @endif
</div>
```

### Step 5: Create Route

Add to `routes/web.php`:
```php
Route::middleware(['auth'])->group(function () {
    Route::get('/clients', App\Livewire\Clients\ClientList::class)->name('clients.index');
});
```

---

## Development Workflow

### Running the Application

```bash
# Terminal 1: Start Laravel server
php artisan serve

# Terminal 2: Watch assets
npm run dev

# Terminal 3: Run queue worker
php artisan queue:work

# Terminal 4: Run Redis (if not running as service)
redis-server
```

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/ClientTest.php

# Run with coverage
php artisan test --coverage
```

### Code Quality

```bash
# Format code with Pint
./vendor/bin/pint

# Run static analysis
./vendor/bin/phpstan analyse

# Check code style
./vendor/bin/pint --test
```

---

## Recommended VS Code Extensions

- PHP Intelephense
- Laravel Extra Intellisense
- Laravel Blade Snippets
- Tailwind CSS IntelliSense
- Livewire Language Support
- Better Comments
- GitLens

---

## Useful Commands

### Artisan Commands

```bash
# Create Livewire component
php artisan make:livewire ComponentName

# Create model with migration and factory
php artisan make:model ModelName -mf

# Clear all caches
php artisan optimize:clear

# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Fresh database with seeders
php artisan migrate:fresh --seed
```

### NPM Commands

```bash
# Watch for changes
npm run dev

# Build for production
npm run build

# Build and watch
npm run watch
```

---

## Next Steps

After completing the quick start:

1. **Complete Sprint 1** (Week 1-2)
   - Finish multi-tenancy setup
   - Implement team member invitations
   - Add user roles and permissions

2. **Build Client Management** (Week 3)
   - Complete client form component
   - Add client import/export
   - Implement client archiving

3. **Create Catalog System** (Week 4-5)
   - Build catalog models and migrations
   - Create catalog management UI
   - Implement categories and search

4. **Start Quote Builder** (Week 6)
   - Create quote models
   - Build basic quote interface
   - Implement line item management

---

## Common Issues & Solutions

### Issue: Livewire assets not loading
**Solution**: Run `php artisan livewire:publish --assets`

### Issue: Tailwind styles not applying
**Solution**:
1. Check content paths in `tailwind.config.js`
2. Run `npm run build`
3. Clear browser cache

### Issue: Queue not processing
**Solution**: Make sure Redis is running and queue worker is started

### Issue: Tenant database not creating
**Solution**: Check tenancy configuration and database permissions

---

## Resources

### Documentation
- [Laravel Docs](https://laravel.com/docs)
- [Livewire Docs](https://livewire.laravel.com)
- [Tailwind CSS Docs](https://tailwindcss.com/docs)
- [Tenancy Package Docs](https://tenancyforlaravel.com/docs)

### Video Tutorials
- [Laracasts - Laravel From Scratch](https://laracasts.com/series/laravel-from-scratch)
- [Laracasts - Livewire](https://laracasts.com/series/livewire)
- [Tailwind Labs - Screencasts](https://www.youtube.com/c/TailwindLabs)

### Community
- [Laravel Discord](https://discord.gg/laravel)
- [Livewire Discord](https://discord.gg/livewire)
- [Reddit - r/laravel](https://www.reddit.com/r/laravel)

---

## Tips for Success

1. **Commit often** - Small, focused commits make debugging easier
2. **Write tests** - Start with feature tests for critical paths
3. **Use Livewire effectively** - Keep components focused and reusable
4. **Optimize early** - Use lazy loading and caching from the start
5. **Document as you go** - Add comments and update docs regularly
6. **Follow Laravel conventions** - Consistent code is maintainable code
7. **Use queues** - Offload heavy tasks to background jobs
8. **Monitor performance** - Use Laravel Telescope in development

---

## Ready to Build!

You now have everything you need to start building Estimo. Follow the roadmap in ROADMAP.md and refer to REQUIREMENTS.md for detailed feature specifications.

Good luck! 🚀
