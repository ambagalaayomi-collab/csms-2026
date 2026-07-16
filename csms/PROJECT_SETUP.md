# CSMS – Organized Laravel Project

## Application flow

1. Client registers, logs in, and submits a project request.
2. Project manager reviews the request and assigns an engineer.
3. Engineer creates the estimate and uploads the technical-report Excel sheet.
4. Project manager creates the proposal PDF from the technical report.
5. Client views the proposal and approves, rejects, or requests changes.

## Code organization

- `routes/web.php` contains route declarations only.
- `app/Http/Controllers/Client` contains client workflows.
- `app/Http/Controllers/Manager` contains manager workflows.
- `app/Http/Controllers/Engineer` contains engineer workflows.
- `app/Http/Middleware/EnsureUserHasRole.php` protects role-specific pages.
- `app/Models` contains relationships, fillable fields, and casts.
- `resources/views` contains role-based UI pages and PDF templates.

## First-time setup

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configure the database values in `.env`, then run:

```bash
php artisan migrate:fresh --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

## Demo accounts

All demo accounts use `Password123!`.

- Client: `client@csms.test`
- Project manager: `manager@csms.test`
- Engineer: `engineer@csms.test`

Change or remove demo credentials before production use.
