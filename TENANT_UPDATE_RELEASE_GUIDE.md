# Tenant Update + Migration + Seed + Manual GitHub Tag (Demo Guide)

This file is a complete, step-by-step guide for your demo in the `rentride` project.

---

## 1) What You Are Building

You will create a **tenant module** named **Students** that has:

- `student_id`
- `name`
- `address`

Then you will:

1. Run tenant migration
2. Run tenant seeding
3. Verify in UI
4. Push to GitHub
5. Create a **manual tag in GitHub UI**
6. Create a release in GitHub UI

---

## 2) Important Multi-Tenant Note

In this project:

- Central migrations are in `database/migrations`
- Tenant migrations must be in `database/migrations/tenant`

`php artisan tenants:migrate` reads from tenant migration path (already configured in `config/tenancy.php`).

---

## 3) Create Students Module (Code + DB)

Open terminal in project root and run:

```powershell
php artisan make:model Student
php artisan make:controller StudentController --resource --model=Student
php artisan make:migration create_students_table --path=database/migrations/tenant
php artisan make:seeder TenantStudentSeeder
```

---

## 4) Edit Migration (Tenant Table)

Edit file in `database/migrations/tenant/*_create_students_table.php`:

```php
Schema::create('students', function (Blueprint $table) {
    $table->id();
    $table->string('student_id')->unique();
    $table->string('name');
    $table->string('address')->nullable();
    $table->timestamps();
});
```

---

## 5) Edit Model

Edit `app/Models/Student.php`:

```php
protected $fillable = [
    'student_id',
    'name',
    'address',
];
```

---

## 6) Create Seeder Data

Edit `database/seeders/TenantStudentSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;

class TenantStudentSeeder extends Seeder
{
    public function run(): void
    {
        Student::updateOrCreate(
            ['student_id' => 'STU-0001'],
            ['name' => 'Juan Dela Cruz', 'address' => 'Manila']
        );

        Student::updateOrCreate(
            ['student_id' => 'STU-0002'],
            ['name' => 'Maria Santos', 'address' => 'Quezon City']
        );
    }
}
```

---

## 7) Add Routes for Students Module

In `routes/web.php`, inside your tenant authenticated area (for admin/staff), add:

```php
use App\Http\Controllers\StudentController;

Route::resource('students', StudentController::class)
    ->middleware('tenant.permission:customers.manage');
```

If you want a different permission, replace middleware with your chosen permission.

---

## 8) Add Controller Logic

In `app/Http/Controllers/StudentController.php`, add at least:

- `index()` -> list all students
- `store()` -> create student
- `update()` -> update student
- `destroy()` -> delete student

Basic index example:

```php
public function index()
{
    $students = Student::latest()->paginate(10);
    return view('admin.students.index', compact('students'));
}
```

---

## 9) Create Blade View (Table List)

Create `resources/views/admin/students/index.blade.php` with a table:

- Student ID
- Name
- Address
- Actions (optional)

This is the "module with table list" that you will show in the demo.

---

## 10) Run Tenant Migration and Seed

### Run for all tenants

```powershell
php artisan tenants:migrate
php artisan tenants:seed --class=TenantStudentSeeder
```

### Run for one tenant only (optional)

```powershell
php artisan tenants:migrate --tenants=<tenant-id>
php artisan tenants:seed --class=TenantStudentSeeder --tenants=<tenant-id>
```

---

## 11) Verify for Demo

1. Login using tenant domain/account
2. Open Students page (for example `/students` or your admin route)
3. Confirm seeded records appear
4. Create one record manually to prove CRUD works

---

## 12) Super Admin Adds/Creates a Tenant (UI Flow)

Use this flow when the Super Admin creates the tenant directly instead of tenant self-signup.

1. Login as Super Admin
2. Go to `Super Admin > Tenants > Add tenant`
3. Fill required fields:
   - Company name
   - Owner name
   - Email
   - Phone
   - Address
   - Subscription plan
   - Domain (optional, can be auto-generated)
4. Submit the form

What the system does in your project:

- Creates tenant as `approved`
- Sets `subscription_expiry` to 1 month
- Enables domain (`is_domain_active = true`)
- Creates tenant admin user
- Provisions tenant routing/domain + tenant DB setup

Manual payment note:

- If you want manual payment tracking for Super Admin-created tenants too, you can either:
  1. Add the same payment fields to `superadmin.tenants.create` form, or
  2. Add payment verification notes after creation in tenant profile.

---

## 13) Git Workflow (Before Tag/Release)

```powershell
git checkout -b feature/tenant-students-module
git add .
git commit -m "Add tenant students module with tenant migration and seeder"
git push -u origin feature/tenant-students-module
```

Create PR on GitHub, review, and merge to `main`.

---

## 14) Prepare `v1.5.0` Release

This repo has a `VERSION` file. Update it to:

```text
v1.5.0
```

Then commit:

```powershell
git checkout main
git pull
git add VERSION
git commit -m "Bump version to v1.5.0"
git push
```

---

## 15) Create Tag + Publish Release

You can do this in either GitHub UI or CLI.

### Option A: GitHub UI (manual, easiest for demo)

After your `main` branch is updated:

1. Open your repository on GitHub
2. Click **Releases** (right panel or repository tab area)
3. Click **Draft a new release**
4. In **Choose a tag**, type your new tag: `v1.5.0`
5. Click **Create new tag: v1.5.0 on publish target: main**
6. Confirm target branch/commit is correct (`main`)
7. Set release title: `v1.5.0`
8. Add release notes, for example:
   - Added tenant update flow improvements
   - Added separate extension request page
   - Refactored validations into Form Request classes
   - Added support for custom staff roles
9. Click **Publish release**

This creates both:

- the Git tag (`v1.5.0`)
- the GitHub Release entry

---

### Option B: Git CLI (if you prefer terminal)

```powershell
git checkout main
git pull
git tag -a v1.5.0 -m "Release v1.5.0"
git push origin v1.5.0
```

Then open GitHub > Releases > **Draft a new release** > choose existing tag `v1.5.0` > add notes > publish.

---

## 16) Demo Script (What to Say/Show)

1. "We added a tenant-specific Students module."
2. Show migration in `database/migrations/tenant`
3. Run `tenants:migrate` and `tenants:seed`
4. Show Students list in UI
5. Show merged PR
6. Show GitHub Release page with tag `v1.5.0`

---

## 17) Quick Troubleshooting

- If `students` table not found: check migration is in `database/migrations/tenant`
- If no seeded data: rerun `php artisan tenants:seed --class=TenantStudentSeeder`
- If route 404: confirm route is inside authenticated tenant middleware group
- If wrong database used: make sure tenancy middleware is active for tenant routes/domain

---

## 18) FAQ: Does "Download Update" Also Apply Migrations to Tenant DB?

Short answer: **it runs central app migrations only**.

Current updater flow in this project runs:

- `php artisan migrate --force`

It does **not** run tenant migration command:

- `php artisan tenants:migrate`

So if your release includes new tenant-table schema changes (in `database/migrations/tenant`), you still need to run tenant migrations separately (for all tenants or specific tenants).

Recommended after update:

```powershell
php artisan tenants:migrate
```

If you want fully automatic tenant schema updates during "Download Update", add `php artisan tenants:migrate --force` to the updater sequence.

---

## 19) Optional: Download/Share This File

Use this file directly:

- `TENANT_UPDATE_RELEASE_GUIDE.md`

You can upload it to class group, send it by chat, or convert to PDF for handout.
