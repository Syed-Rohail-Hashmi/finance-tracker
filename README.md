# Finance Tracker

A personal finance tracker made with PHP and MySQL. Each user registers, logs in, and manages their own income and expense transactions. A dashboard and charts (bar, line and pie) show where the money goes.

## Built With

- PHP
- MySQL
- HTML, CSS and JavaScript
- pChart (charts)
- Composer (loads the `.env` reader)

## How to Run

1. Install XAMPP and start Apache and MySQL.
2. Download this project and put the folder in `C:\xampp\htdocs` with the name `finance-tracker`.
3. Install Composer, open Command Prompt inside the project folder, and run: `composer install`
4. Open `localhost/phpmyadmin`, click Import, choose `database/schema.sql`, and click Import. This creates the `finance_tracker` database and its tables.
5. In the project folder, make a copy of `.env.example` and name the copy `.env`.
6. Open `.env` and check the database details. With the default XAMPP settings, `DB_USER=root` and an empty `DB_PASS=` are correct.
7. Open `localhost/finance-tracker` in your browser and register a new account.

The `.env` file and the `vendor` folder are not uploaded to GitHub on purpose.

Author: Syed Rohail Hashmi
