# PHP ADR Boilerplate

This is a PHP boilerplate following the Action-Domain-Responder (ADR) architecture.

## Getting Started

To create a new project using this boilerplate, run the following command in your terminal:

```bash
composer create-project hartpableo/php-adr my-project --repository='{"type":"vcs","url":"https://github.com/hartpableo/php-adr-boilerplate"}'
```

*(Note: Replace `hartpableo/php-adr` and the URL with your actual private repository details if they differ.)*

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
