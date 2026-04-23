# N.I.K.A — Project Setup Guide

Complete instructions for getting this project running on a new machine from scratch, with separate notes for macOS and Windows users.

---

## Before You Start

This project is a Laravel application that uses:

- PHP 8.3+
- Composer 2
- Node.js 18+
- npm 9+
- SQLite

For most developers, the easiest local setup is:

- **macOS:** Laravel Herd + Node.js + Git
- **Windows:** Laravel Herd for Windows + Node.js + Git

If you already manage PHP and Composer another way, that is fine too. The commands in this guide still apply.

### Recommended Terminal

- **macOS:** Terminal or iTerm
- **Windows:** PowerShell

The examples below use standard shell commands. Where Windows commands differ, they are shown explicitly.

---

## Prerequisites

Before opening the project, make sure the machine has the required tools installed.

### Required Software

| Software | Minimum Version | Check Command |
|---|---|---|
| PHP | 8.3+ | `php -v` |
| Composer | 2.x | `composer -V` |
| Node.js | 18+ | `node -v` |
| npm | 9+ | `npm -v` |
| Git | Any recent version | `git --version` |

### Required PHP Extensions

Run `php -m` and confirm these are available:

- `pdo`
- `pdo_sqlite`
- `mbstring`
- `openssl`
- `tokenizer`
- `xml`
- `ctype`
- `json`
- `bcmath`
- `fileinfo`

SQLite is the default database for this project, so `pdo_sqlite` is required.

---

## macOS Setup

### Option A — Recommended: Laravel Herd

Laravel Herd is the simplest way to get PHP, Composer, and local Laravel sites working on macOS.

1. Install Herd.
2. Install Node.js 18+.
3. Install Git if it is not already available.
4. Confirm these commands work:

```bash
php -v
composer -V
node -v
npm -v
git --version
```

If `php` or `composer` are not found after installing Herd, open a new terminal window and re-run the checks.

### Option B — Manual Tooling

If you are not using Herd, install:

- PHP 8.3 or newer
- Composer 2
- Node.js 18 or newer
- Git

Then verify the same version commands shown above.

---

## Windows Setup

### Option A — Recommended: Laravel Herd for Windows

Laravel Herd for Windows is the easiest way to get PHP, Composer, and local Laravel projects running consistently.

1. Install Herd for Windows.
2. Install Node.js 18+.
3. Install Git for Windows.
4. Open **PowerShell** and confirm:

```powershell
php -v
composer -V
node -v
npm -v
git --version
```

If PowerShell says a command is not recognized, close it, reopen it, and try again. On Windows this often just means the PATH was not refreshed yet.

### Option B — Manual Tooling

If you are not using Herd, install:

- PHP 8.3 or newer
- Composer 2
- Node.js 18 or newer
- Git

Then re-run the same version checks.

### Windows Notes

- PowerShell is preferred over Command Prompt.
- If scripts are blocked, use the normal `npm` commands first before changing execution policy.
- If path issues persist, restart the terminal or sign out and back in.

---

## Step 1 — Get the Code

### Option A: Clone from Git

Replace the placeholder repository URL with the real one:

```bash
git clone https://github.com/YOUR_USERNAME/YOUR_REPO_NAME.git
cd YOUR_REPO_NAME
```

### Option B: Copy the Project Folder

If you are moving the project by USB drive, AirDrop, network share, or zip file:

1. Copy the full project directory to the new machine.
2. Open a terminal in that folder.
3. Reinstall dependencies locally instead of copying `vendor/` or `node_modules/`.

Important notes:

- `vendor/` can be omitted because `composer install` recreates it.
- `node_modules/` can be omitted because `npm install` recreates it.
- `database/database.sqlite` should be kept if you want to preserve existing local data.
- `.env` should usually not be copied unless you intentionally want the exact same local environment values.

---

## Step 2 — Install PHP Dependencies

Run:

```bash
composer install
```

This reads `composer.json` and installs all PHP dependencies into `vendor/`.

If this fails:

- Check `php -v` and confirm you are on PHP 8.3 or newer.
- Check `php -m` and confirm the required extensions are installed.
- Re-run the command after fixing the missing dependency.

---

## Step 3 — Create the Environment File

Create `.env` from the example file.

### macOS / Linux

```bash
cp .env.example .env
```

### Windows PowerShell

```powershell
Copy-Item .env.example .env
```

### Windows Command Prompt

```cmd
copy .env.example .env
```

Then generate the application key:

```bash
php artisan key:generate
```

You should see:

```text
Application key set successfully.
```

### Review `.env`

Open `.env` and confirm the local defaults are sensible:

```dotenv
APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

This project uses **SQLite by default**, so you do not need MySQL or PostgreSQL for local setup unless you intentionally want to switch databases.

---

## Step 4 — Create the SQLite Database File

The SQLite file must exist before migrations run.

### macOS / Linux

```bash
touch database/database.sqlite
```

### Windows PowerShell

```powershell
New-Item -ItemType File -Path database\database.sqlite -Force
```

### Windows Command Prompt

```cmd
type nul > database\database.sqlite
```

After this step, make sure the file exists at:

```text
database/database.sqlite
```

---

## Step 5 — Run Migrations

Run:

```bash
php artisan migrate
```

This creates the application tables in SQLite.

If the database is brand new, this should finish cleanly. If you copied an older database and see schema conflicts, use:

```bash
php artisan migrate:fresh
```

Use `migrate:fresh` only if wiping the local database is acceptable.

---

## Step 6 — Seed the Database

Run:

```bash
php artisan db:seed
```

This loads the demo data used throughout the project.

After seeding, these accounts should exist:

| Role | Email | Password |
|---|---|---|
| Admin | `admin@example.com` | `password` |
| Test User | `test@example.com` | `password` |

The seeders also create:

- sample food items
- sample dietitians
- sample meal plan templates
- sample feedback data

---

## Step 7 — Install Node Dependencies

Run:

```bash
npm install
```

This installs the frontend build tooling into `node_modules/`.

If `npm install` fails:

- confirm `node -v` is 18 or newer
- confirm `npm -v` is available
- re-run the command after fixing the Node installation

---

## Step 8 — Build Frontend Assets

Run:

```bash
npm run build
```

This compiles the frontend assets into `public/build/`.

If this step is skipped, the application may load with missing CSS or JavaScript.

---

## Step 9 — Run the Application

### Option A — If You Are Using Laravel Herd

Open the local `.test` site URL that Herd assigns to this project. Herd handles serving the application for you.

If the site does not appear in Herd yet:

1. Make sure the project folder is inside a location Herd watches, or add it manually in Herd.
2. Reopen Herd if needed.
3. Visit the URL shown by Herd for the project.

### Option B — If You Are Not Using Herd

Run:

```bash
php artisan serve
```

Then open:

```text
http://127.0.0.1:8000
```

If port `8000` is already in use:

```bash
php artisan serve --port=8001
```

Then visit:

```text
http://127.0.0.1:8001
```

### Live Development Mode

If you want the Laravel server, queue worker, logs, and Vite dev server running together:

```bash
composer run dev
```

Use this during active development when you want automatic frontend rebuilding.

---

## Step 10 — Verify Everything Works

After the app is running:

1. Open `/login`
2. Log in with `test@example.com` / `password`
3. Confirm the user dashboard loads
4. Open `/admin/login`
5. Log in with `admin@example.com` / `password`
6. Confirm the admin dashboard loads

If you are using `php artisan serve`, the full URLs are:

- `http://127.0.0.1:8000/login`
- `http://127.0.0.1:8000/admin/login`

---

## One-Command Setup Shortcut

The project includes a setup script:

```bash
composer run setup
```

This runs:

1. `composer install`
2. create `.env` from `.env.example` if needed
3. `php artisan key:generate`
4. `php artisan migrate --force`
5. `npm install`
6. `npm run build`

Important:

- You should still create `database/database.sqlite` **before** running `composer run setup`.
- You should still run `php artisan db:seed` afterward if you want the demo users and sample data.

Recommended shortcut flow:

1. create `database/database.sqlite`
2. run `composer run setup`
3. run `php artisan db:seed`

---

## Troubleshooting

### `No application encryption key has been specified`

Run:

```bash
php artisan key:generate
```

### `Database file does not exist` or SQLite connection errors

You likely skipped the database file step.

### macOS / Linux

```bash
touch database/database.sqlite
```

### Windows PowerShell

```powershell
New-Item -ItemType File -Path database\database.sqlite -Force
```

Then re-run:

```bash
php artisan migrate
```

### Pages load but the UI looks unstyled

Run:

```bash
npm run build
```

If you are actively developing the frontend, use:

```bash
composer run dev
```

### `Class not found` or autoload errors

Run:

```bash
composer dump-autoload
```

### Migrations fail because tables already exist

If you are okay resetting local data:

```bash
php artisan migrate:fresh --seed
```

Warning: this wipes the local database and rebuilds it from scratch.

### `Permission denied` on `storage` or `bootstrap/cache`

On macOS / Linux:

```bash
chmod -R 775 storage bootstrap/cache
```

This is usually not a Windows issue.

### Composer install fails because of PHP version

This project requires **PHP 8.3+**.

Check:

```bash
php -v
```

If multiple PHP versions are installed, make sure your terminal is using the correct one before running `composer install`.

### `php`, `composer`, `node`, or `npm` is not recognized

This is almost always a PATH issue.

- On **macOS**, open a new terminal window and try again.
- On **Windows**, reopen PowerShell and try again.
- If that still fails, confirm the tool was actually installed and added to PATH.

---

## Creating a New Admin Account

If you need an additional admin account instead of the seeded one, go to:

```text
/admin/register
```

If you are using `php artisan serve`, the full URL is:

```text
http://127.0.0.1:8000/admin/register
```

Use the admin registration code:

```text
1000808790
```

---

## Project Structure Quick Reference

```text
app/
  Http/Controllers/     -> Controllers
  Http/Requests/        -> Validation request classes
  Models/               -> Eloquent models
database/
  migrations/           -> Database schema
  seeders/              -> Demo and initial data
  database.sqlite       -> SQLite database file
resources/
  views/                -> Blade templates
  css/app.css           -> Frontend CSS entry
  js/app.js             -> Frontend JS entry
routes/
  web.php               -> Web routes
public/build/           -> Compiled frontend assets
.env                    -> Local environment settings
```

---

## Running Tests

Run the full test suite with:

```bash
php artisan test --compact
```

Run a specific test with:

```bash
php artisan test --compact --filter=LoginTest
```

---

## Summary Checklist

- [ ] PHP 8.3+, Composer, Node 18+, npm, and Git installed
- [ ] project code copied or cloned
- [ ] `composer install` completed
- [ ] `.env` created from `.env.example`
- [ ] `php artisan key:generate` completed
- [ ] `database/database.sqlite` created
- [ ] `php artisan migrate` completed
- [ ] `php artisan db:seed` completed
- [ ] `npm install` completed
- [ ] `npm run build` completed
- [ ] app opened through Herd or `php artisan serve`
- [ ] test and admin logins working
