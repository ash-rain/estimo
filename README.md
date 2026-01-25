# Estimo - Industry-Specific Quoting & Cost Estimation SaaS

Multi-industry SaaS platform for creating professional quotes and cost estimations, built with Laravel 12, Livewire 3, and Tailwind CSS.

## Tech Stack

- **Backend**: Laravel 12
- **Frontend**: Livewire 3 + Alpine.js + Tailwind CSS
- **Database**: SQLite (development) / MySQL/PostgreSQL (production)
- **Multi-Tenancy**: Stancl/Tenancy
- **Dev Tools**: Laravel Debugbar, PHPUnit

## Installation Complete ✅

The project has been successfully set up with:

- ✅ Laravel 12 installed
- ✅ Livewire 3 configured
- ✅ Multi-tenancy package (stancl/tenancy) installed
- ✅ Tailwind CSS configured with custom theme
- ✅ Alpine.js integrated
- ✅ Development tools installed
- ✅ Database migrations run
- ✅ Frontend assets compiled

## Quick Start

### Run the Development Server

```bash
# Start Laravel server
php artisan serve

# In another terminal, watch for asset changes
npm run dev
```

Visit: http://estimo.test:8000 (or http://localhost:8000)

### Run Migrations

```bash
php artisan migrate
```

### Build Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### Run Tests

```bash
php artisan test
```

## Project Structure

```
estimo/
├── app/
│   ├── Livewire/          # Livewire components
│   ├── Models/            # Eloquent models
│   ├── Services/          # Business logic services
│   └── Providers/         # Service providers
├── config/
│   └── tenancy.php        # Multi-tenancy configuration
├── database/
│   ├── migrations/        # Central database migrations
│   └── migrations/tenant/ # Tenant database migrations
├── resources/
│   ├── css/
│   │   └── app.css        # Tailwind CSS
│   ├── js/
│   │   └── app.js         # Alpine.js setup
│   └── views/             # Blade templates
├── routes/
│   ├── web.php            # Central routes
│   └── tenant.php         # Tenant routes
├── REQUIREMENTS.md        # Detailed feature requirements
├── ROADMAP.md            # 28-week development roadmap
├── PROJECT_SUMMARY.md    # Project overview
└── QUICK_START.md        # Development guide
```

## Multi-Tenancy Setup

The project uses subdomain-based multi-tenancy. Each tenant (workspace) will have their own subdomain:

- Central domain: `estimo.test`
- Tenant domains: `{tenant}.estimo.test`

### Local Development Setup

Add tenant subdomains to your `/etc/hosts` file:

```
127.0.0.1 estimo.test
127.0.0.1 demo.estimo.test
127.0.0.1 test.estimo.test
```

## Next Steps

Follow the roadmap in `ROADMAP.md`. The immediate next steps are:

### Week 1-2: Sprint 1 - Infrastructure
1. **Complete Multi-Tenancy Setup**
   - Configure tenant identification (subdomain/path)
   - Set up tenant database scoping
   - Test tenant isolation

2. **Implement Authentication**
   - Install Laravel Breeze with Livewire
   - Customize for multi-tenancy
   - Create registration flow that creates workspace

3. **User Management**
   - Team member invitation system
   - Role-based access control
   - User profile management

### Week 3: Sprint 2 - Client Management
1. Create client models and migrations
2. Build Livewire components for client CRUD
3. Implement search and filtering
4. Add CSV import/export

### Week 4-5: Sprint 3-4 - Catalog System
1. Build product/service catalog
2. Category management
3. Bulk import/export
4. Unit types and variants

### Week 6: Sprint 5 - Quote Builder
1. Create quote models and migrations
2. Build quote builder interface
3. Implement line item management
4. Real-time calculations

## Available Commands

```bash
# Artisan commands
php artisan serve              # Start development server
php artisan migrate            # Run migrations
php artisan migrate:fresh      # Fresh database
php artisan test               # Run tests
php artisan make:livewire Name # Create Livewire component
php artisan tenancy:migrate    # Run tenant migrations

# NPM commands
npm run dev                    # Watch assets
npm run build                  # Build for production

# Code quality
./vendor/bin/pint              # Format code
php artisan test --coverage    # Test with coverage
```

## Development Workflow

1. **Create a new feature branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. **Make your changes**
   - Follow Laravel best practices
   - Keep components focused and reusable
   - Write tests for critical functionality

3. **Run tests and code formatting**
   ```bash
   ./vendor/bin/pint
   php artisan test
   ```

4. **Commit your changes**
   ```bash
   git add .
   git commit -m "Add: Your feature description"
   ```

## Environment Configuration

Key environment variables in `.env`:

```env
APP_NAME=Estimo
APP_URL=http://estimo.test

DB_CONNECTION=sqlite          # Use mysql/pgsql for production

QUEUE_CONNECTION=database     # Use redis for production
CACHE_STORE=database          # Use redis for production

MAIL_MAILER=log              # Configure for production
```

## Resources

### Documentation
- [Laravel Documentation](https://laravel.com/docs)
- [Livewire Documentation](https://livewire.laravel.com)
- [Tailwind CSS](https://tailwindcss.com/docs)
- [Tenancy Package](https://tenancyforlaravel.com/docs)

### Project Documentation
- `REQUIREMENTS.md` - Feature specifications
- `ROADMAP.md` - Development timeline
- `PROJECT_SUMMARY.md` - Quick reference
- `QUICK_START.md` - Setup guide

## Support & Contribution

For questions about the project plan or implementation, refer to the documentation files in the root directory.

## License

Proprietary - All rights reserved

---

**Ready to build!** 🚀

Start by following Sprint 1 in `ROADMAP.md`.
