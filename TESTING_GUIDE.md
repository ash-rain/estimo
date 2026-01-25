# Estimo - Testing Guide

## Quick Test: Sprint 1 Multi-Tenancy

### Prerequisites

1. **Update /etc/hosts file:**
   ```bash
   sudo nano /etc/hosts
   ```

   Add these lines:
   ```
   127.0.0.1 estimo.test
   127.0.0.1 demo-company.estimo.test
   127.0.0.1 test-company.estimo.test
   ```

   Save and exit (Ctrl+O, Enter, Ctrl+X)

2. **Start the development server:**
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```

   Keep this terminal running.

3. **In another terminal, watch assets (optional):**
   ```bash
   npm run dev
   ```

---

## Test 1: Register New Tenant

### Steps:

1. **Visit central domain:**
   ```
   http://estimo.test:8000/register
   ```

2. **Fill registration form:**
   - Company Name: `Test Company`
   - Your Name: `John Doe`
   - Email: `john@testcompany.com`
   - Password: `password123`
   - Confirm Password: `password123`

3. **Click "Register"**

### Expected Result:
- ✅ Redirects to tenant dashboard
- ✅ URL changes to `test-company.estimo.test:8000/dashboard`
- ✅ Dashboard shows:
  - Company: Test Company
  - Tenant ID: (UUID)
  - Your Role: Owner
  - Plan: Starter
  - Trial Ends: (14 days from now)

---

## Test 2: Login to Existing Tenant

### Steps:

1. **Visit demo tenant domain:**
   ```
   http://demo-company.estimo.test:8000
   ```

2. **Should redirect to login page:**
   ```
   http://demo-company.estimo.test:8000/login
   ```

3. **Login with test credentials:**
   - Email: `demo@example.com`
   - Password: `password`

4. **Click "Log in"**

### Expected Result:
- ✅ Successfully logs in
- ✅ Redirects to `demo-company.estimo.test:8000/dashboard`
- ✅ Dashboard shows Demo Company information
- ✅ User role shows as "Owner"

---

## Test 3: Tenant Isolation

### Steps:

1. **While logged into Demo Company, try to access Test Company:**
   ```
   http://test-company.estimo.test:8000/dashboard
   ```

### Expected Result:
- ✅ Redirects to login (session not shared between tenants)
- ✅ Cannot access other tenant's data

---

## Test 4: Create Tenant via Command

### Steps:

1. **Run the command:**
   ```bash
   php artisan tenant:create "Acme Inc" "admin@acme.com" --password=secret123
   ```

2. **Note the generated subdomain**

3. **Add subdomain to /etc/hosts:**
   ```
   127.0.0.1 acme-inc.estimo.test
   ```

4. **Visit:**
   ```
   http://acme-inc.estimo.test:8000
   ```

5. **Login:**
   - Email: `admin@acme.com`
   - Password: `secret123`

### Expected Result:
- ✅ Tenant created successfully
- ✅ Can login and access dashboard
- ✅ Shows Acme Inc information

---

## Test 5: Central Domain

### Steps:

1. **Visit central domain:**
   ```
   http://estimo.test:8000
   ```

### Expected Result:
- ✅ Shows Laravel welcome page
- ✅ No tenant context
- ✅ Can access registration page

---

## Test 6: Database Verification

### Steps:

1. **Check tenants in database:**
   ```bash
   php artisan tinker
   ```

   ```php
   \App\Models\Tenant::with('domains')->get()
   ```

### Expected Result:
```
Collection {
  0: App\Models\Tenant {
    id: "...",
    name: "Demo Company",
    email: "demo@example.com",
    plan: "starter",
    domains: Collection {
      0: Stancl\Tenancy\Database\Models\Domain {
        domain: "demo-company.estimo.test"
      }
    }
  },
  1: App\Models\Tenant {
    id: "...",
    name: "Test Company",
    ...
  }
}
```

2. **Check users in tenant:**
   ```php
   $tenant = \App\Models\Tenant::first();
   tenancy()->initialize($tenant);
   \App\Models\User::all();
   ```

### Expected Result:
- Shows owner user for that tenant
- Email matches tenant email

---

## Test 7: Authentication Features

### Test Email Verification (Placeholder)
- ✅ Email verification ready (emails log to `storage/logs/laravel.log` in dev)

### Test Password Reset
1. **Click "Forgot password" on login page**
2. **Enter email**
3. **Check logs for reset link**

---

## Test 8: Dashboard Features

### Navigate Around

1. **Click "Profile" in navigation**
   - ✅ Profile page loads
   - ✅ Shows user information

2. **Click "Dashboard" in navigation**
   - ✅ Returns to dashboard
   - ✅ Tenant context preserved

3. **Logout and login again**
   - ✅ Session maintained
   - ✅ Remember me works (if selected)

---

## Test 9: Multi-User Testing

### Create Second User for Tenant

Currently requires database access. In Sprint 2, we'll add the UI for this.

**Manual method:**
```bash
php artisan tinker
```

```php
$tenant = \App\Models\Tenant::where('email', 'demo@example.com')->first();
tenancy()->initialize($tenant);

\App\Models\User::create([
    'name' => 'Jane Manager',
    'email' => 'jane@democompany.com',
    'password' => bcrypt('password'),
    'role' => 'manager',
    'email_verified_at' => now(),
]);
```

**Test:**
1. Logout
2. Login as jane@democompany.com / password
3. ✅ Dashboard shows role as "Manager"

---

## Test 10: Error Handling

### Test Invalid Tenant
1. **Visit non-existent subdomain:**
   ```
   http://nonexistent.estimo.test:8000
   ```

### Expected Result:
- ✅ Shows 404 or redirects appropriately
- ✅ No errors in console

---

## Debugging Tips

### Check Logs
```bash
tail -f storage/logs/laravel.log
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Check Database Files (SQLite)
```bash
ls -la database/*.sqlite
```

### Verify Migrations
```bash
# Central database
php artisan migrate:status

# Tenant databases
php artisan tenants:migrate --pretend
```

---

## Common Issues

### Issue: "Tenant could not be identified"
**Solution:**
1. Check /etc/hosts has the subdomain
2. Verify domain exists in database
3. Clear browser cache

### Issue: "CSRF token mismatch"
**Solution:**
1. Clear browser cookies
2. Restart dev server
3. Clear Laravel cache

### Issue: "Class 'Tenant' not found"
**Solution:**
```bash
composer dump-autoload
```

### Issue: Can't access subdomain
**Solution:**
1. Use `--host=0.0.0.0` when running `php artisan serve`
2. Update /etc/hosts
3. Clear browser DNS cache

---

## Performance Testing

### Measure Registration Time
```bash
time curl -X POST http://estimo.test:8000/register \
  -d "company_name=Speed Test" \
  -d "name=Test User" \
  -d "email=speed@test.com" \
  -d "password=password" \
  -d "password_confirmation=password"
```

Target: < 1 second

### Measure Login Time
Target: < 300ms

### Measure Dashboard Load
Target: < 500ms

---

## Success Checklist

- [ ] Can register new tenant from central domain
- [ ] Registration creates tenant + domain + user
- [ ] Can login to tenant subdomain
- [ ] Dashboard shows correct tenant information
- [ ] User role displayed correctly
- [ ] Tenant isolation works (can't access other tenants)
- [ ] Command creates tenants successfully
- [ ] Profile page accessible
- [ ] Logout works correctly
- [ ] Password reset flow works

---

## Next: Start Testing!

Run through all tests to verify Sprint 1 implementation. Report any issues found.

**Ready to test!** 🧪
