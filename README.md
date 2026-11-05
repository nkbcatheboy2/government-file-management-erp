# Government File Management ERP

A PHP and MySQL file-register and movement-tracking application.

## Requirements

- PHP 8.0 or later with the `mysqli` and `session` extensions
- MySQL or MariaDB

## Local setup

1. Create a database named `file_tracker_erp`.
2. Import `database_schema.sql` using phpMyAdmin or the MySQL command line.
3. Copy `config.example.php` to `config.php`.
4. Set the database host, username, password, and name in `config.php`.
5. Set a login username and a password hash. Generate the hash with:

   ```sh
   php -r "echo password_hash('choose-a-strong-password', PASSWORD_DEFAULT), PHP_EOL;"
   ```

6. Open `login.php` through your PHP web server and sign in.

## Hosting and security

- Create a new, empty database for a demo. Do not upload real or confidential government records to public free hosting.
- Import `database_schema.sql` into the hosting provider's database.
- Upload the application files and a provider-specific `config.php`. Never commit or upload `config.php` to GitHub; it is excluded by `.gitignore` and blocked by `.htaccess`.
- Use a unique, strong manager password and enable HTTPS at the hosting provider.
- Do not upload the local database or Git `.git` directory.
