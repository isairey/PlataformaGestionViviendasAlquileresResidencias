# 🏡 HomeRoom

HomeRoom is a digital platform designed to streamline the financial and organizational tasks involved in housing and rental management. Built with both landlords and tenants in mind, HomeRoom will simplifies rental processes by offering a centralized system to handle administrative responsibilities, payment tracking, and communication between parties.

For landlords, HomeRoom provides an efficient way to manage households, monitor payment statuses, and oversee tenant activity. For tenants, it offers an accessible tool to track monthly dues, view rental history, and stay updated with household announcements.

Through an intuitive and well-organized interface, HomeRoom fosters seamless interaction between landlords and tenants. It promotes clear communication, reduces the complexity of managing rentals, and ultimately contributes to a more transparent and efficient housing experience for everyone involved.

## 🚀 Getting Started

Follow these steps to get the project running locally.
Laravel Herd is the recommended environment for this project, but alternatives are included below.

---

### 1. Set Up the Project Directory

If you're using **Laravel Herd**, run:

```bash
cd ~/Herd
```

Otherwise, use any location you prefer:

```bash
cd <your-project-folder>
```

Then clone the repository:

```bash
git clone https://github.com/COMP-016-Web-Development-Group-1/HomeRoom.git
```

After cloning, make sure you enter the project folder:

```bash
cd HomeRoom
```

---

### 2. Install Dependencies

```bash
composer install
npm install
npm run build
```

---

### 3. Environment Configuration

Copy the example `.env` file:

```bash
cp .env.example .env
```

Then open `.env` and fill in the following fields:

```env
# Our custom environment variables
DEFAULT_LANDLORD_EMAIL=landlord@gmail.com
```

> Note:
> By default, we’re using log-only email for local development. This means emails won’t actually be sent. They’ll just be written to the log file located at: `storage/logs/laravel.log`

If we ever need to showcase real email sending (e.g. for demo or production), then:

1. **Comment out** the log mailer block:

    ```env
    # MAIL_MAILER=log
    # MAIL_SCHEME=null
    # MAIL_HOST=127.0.0.1
    # MAIL_PORT=2525
    # MAIL_USERNAME=null
    # MAIL_PASSWORD=null
    # MAIL_FROM_ADDRESS="hello@example.com"
    # MAIL_FROM_NAME="${APP_NAME}"
    ```

2. **Uncomment** the Gmail SMTP block:
    ```env
    MAIL_MAILER=smtp
    MAIL_HOST=smtp.gmail.com
    MAIL_PORT=465
    MAIL_USERNAME=
    MAIL_PASSWORD=
    MAIL_ENCRYPTION=tls
    MAIL_FROM_ADDRESS=
    MAIL_FROM_NAME="${APP_NAME}"
    ```
    We’ll provide the Gmail credentials privately, so you don’ need to create a Gmail account.

If you're using Laravel Herd, make sure to set:

```env
APP_URL=http://homeroom.test
```

Otherwise, keep the default:

### 4. Create the SQLite Database

This project uses SQLite for local development.

#### Mac/Linux or Git Bash:

```bash
touch database/database.sqlite
```

#### PowerShell:

```powershell
New-Item -ItemType File -Path "database/database.sqlite"
```

#### CMD:

```cmd
type NUL > database\database.sqlite
```

Alternatively, you can **manually create a blank file** named `database.sqlite` in the `database` directory.

---

### 5. Generate App Key

```bash
php artisan key:generate
```

---

### 6. Link Storage

```bash
php artisan storage:link
```

---

### 7. Migrate and Seed the Database

```bash
php artisan migrate --seed
```

---

### 8. Serve the Application

If you're using **Laravel Herd**, simply visit:

```
http://homeroom.test
```

If you're **not using Herd**, start the server with:

```bash
php artisan serve
```

Then visit the URL provided in the terminal, e.g.:

```
http://127.0.0.1:8000
```

> Note: the default password for the landlord is `password`

---

## 🛠️ Developing

Common tasks while working on the project:

### Recompile Frontend Assets

Use this to build and watch for changes during development:

```bash
composer run dev
```

> Note: This runs `npm run dev` internally, but keeps everything Laravel-flavored.

---

### Reset the Database

Use this to wipe and re-seed your database:

```bash
php artisan migrate:fresh --seed
```

---

### Run Tests

```bash
php artisan test
```

---

### Format Code

```bash
vendor/bin/pint
```

### Clean Up Temporary Files in Storage

```bash
php artisan app:clean-temp-storage [public/private]
```

### Clean Up Files in Storage

```bash
php artisan app:clean-storage [public/private]
```

> Note: `[]` indicates a choice

### Clearing Cache
To clear various caches during development, run:

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

> Tip: If you encounter unexpected behavior, clearing these caches often resolves common issues.

When deploying to production, rebuild the caches for better performance:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

---

## 🤝 Contributing

### Contribution Guide

1. Ensure you're on the latest `main` branch:

    ```bash
    git checkout main
    git pull origin main
    ```

2. Create a new branch:

    ```bash
    git checkout -b <type>/<short-task-desc>
    ```

    **Examples:**

    - `feat/login-form`
    - `fix/navbar-alignment`
    - `docs/update-readme`

    **Types:**

    - `feat` → New feature
    - `fix` → Bug fix
    - `refactor` → Code clean-up
    - `chore` → Config or dependency update
    - `docs` → Documentation changes

3. Confirm your current branch:

    ```bash
    git branch
    ```

4. Commit your work:

    ```bash
    git add -A
    git commit -m "<type>: <short-description>"
    ```

5. Push to GitHub:

    ```bash
    git push origin <your-branch-name>
    ```

6. Open a Pull Request:

    - Go to: [GitHub Pull Requests](https://github.com/COMP-016-Web-Development-Group-1/HomeRoom/pulls)
    - Base: `main`, Compare: your branch
    - Add a clear title and description
    - ✅ **Make sure your PR passes all checks**  
      (Code style via `lint.yml`, tests via `pest.yml`)
    - If any checks don’t pass, just push your fixes. GitHub will re-run them automatically.

7. **Notify the team**

    Let the team know in the Messenger group chat once your PR is ready or if you need help.

## 📚 Resources & Documentation

Helpful references for understanding and contributing to the project:

### General

-   [Laravel Documentation](https://laravel.com/docs) _(Look for the Basics section)_
-   [Everything You Need to Know About Laravel in 30 Minutes](https://www.youtube.com/watch?v=e7z6KJkGhmg)
-   [From Blank to Blog With Laravel in 10 Minutes](https://www.youtube.com/watch?v=Miea-1jTYl0)

### Frontend

-   [Creating Laravel Blade Component: Step-By-Step](https://www.youtube.com/watch?v=kfvLppwhmgQ)
-   [Laravel Blade Directives](https://kritimyantra.com/blogs/laravel-12-blade-directives-from-beginner-to-advanced)
-   [Tailwind Documentation](https://tailwindcss.com/)
-   [Phosphor Icons](https://phosphoricons.com/)

### Backend

-   [Laravel Controllers](https://laravel.com/docs/12.x/controllers)
-   [Laravel Authorization](https://laravel.com/docs/12.x/authorization)
-   [Laravel Routing](https://laravel.com/docs/12.x/routing)
