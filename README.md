# PHP ADR Boilerplate

This is a PHP boilerplate following the Action-Domain-Responder (ADR) architecture.

## Getting Started

To create a new project in a **new directory**, run:

```bash
composer create-project hartpableo/php-adr my-project --repository='{"type":"vcs","url":"https://github.com/hartpableo/php-adr.git"}'
```

### Installing into an existing directory

If you already have a directory and want to initialize it with this boilerplate:

1. **Clean the directory** (or ensure it's empty).
2. Run:
   ```bash
   composer create-project hartpableo/php-adr . --repository='{"type":"vcs","url":"https://github.com/hartpableo/php-adr.git"}'
   ```
3. Alternatively, if the directory is **not empty** (contains other files you want to keep), manually copy the files from the repository and run:
   ```bash
   composer install
   ```
   *(Wait for it to finish and it will automatically run the `post-create-project-cmd` scripts if you cloned it. If you manually copied it, you might need to run `composer run-script post-create-project-cmd` manually.)*

> **Note**: `composer require` is **not recommended** for this boilerplate because it's a project skeleton, not a library. It defines the root structure (like `public/` and `src/`), which would be hidden in your `vendor/` folder if you "required" it.

### Versions and Releases

Since there are releases available, you can also specify a version:

```bash
composer create-project hartpableo/php-adr:^1.0 my-project --repository='{"type":"vcs","url":"https://github.com/hartpableo/php-adr.git"}'
```

*(Note: Replace `^1.0` with your desired version.)*

### Prerequisites

- PHP 8.3 or higher
- Composer 2
- MySQL/MariaDB (or any PDO-supported database)

### Installation Steps

1. **Run the composer command** (as shown above).
2. **Configure your environment**:
   The installation process automatically copies `.env.example` to `.env.local`. Open `.env.local` and update your database credentials:
   ```env
   DB_DSN='mysql:host=localhost;dbname=your_db_name'
   DB_USERNAME='your_username'
   DB_PASSWORD='your_password'
   ```
3. **Run Migrations**:
   Initialize your database schema:
   ```bash
   php bin/db/migrate
   ```
4. **Start the Development Server**:
   You can use the built-in PHP server:
   ```bash
   php -S localhost:8000 -t public
   ```
   Or use DDEV if you have it installed:
   ```bash
   ddev start
   ```

## Architecture Overview

This project follows the **Action-Domain-Responder** pattern:

- **Action**: Handles the HTTP request, collects input, and calls the Domain.
- **Domain**: Contains the business logic, entities, and repository interfaces.
- **Responder**: Handles the HTTP response (HTML via Twig, JSON, Redirects).
- **Infrastructure**: Implements the repository interfaces (SQL queries).

For more detailed information on the architecture, see [JUNIE.md](JUNIE.md).

## Project Structure

- `bin/`: CLI scripts (migrations, console commands).
- `public/`: Web root (index.php, assets).
- `src/`: Application source code.
- `templates/`: Twig templates.
- `migrations/`: SQL and PHP migration scripts.
- `var/`: Cache and log files.

## License

Private - All rights reserved.
