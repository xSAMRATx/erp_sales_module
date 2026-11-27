# ERP Sales Module

A small sales module for an ERP system, built with **Laravel 12** and **Blade + Tailwind CSS**.
It lets a logged-in user record sales for customers, with several products per sale, quantities and discounts.

## Features

- **Authentication** with Laravel Breeze (login, register, password reset, profile)
- **Sales list** with customer, products, amount and date (paginated)
- **Create sale** form:
  - pick a customer
  - add multiple products, each with quantity and discount percentage
  - total amount and sale date (today or later)
  - saved with AJAX and validated on the server (errors returned as JSON)
- **Edit sale** page that loads the sale's products, quantities and discounts
- Data model: customers, products (price + stock), sales, sale items, and polymorphic notes
- Sales use soft deletes
- Dummy data seeder for customers and products

## Status

This is a work in progress. Updating and deleting a sale are not finished yet (`SaleController@update` and `@destroy`).

## Tech stack

- PHP 8.2+, Laravel 12, Laravel Breeze
- Blade, Tailwind CSS, Alpine.js, Vite
- SweetAlert2 for alerts
- SQLite by default (MySQL works too)

## Getting started

```bash
git clone https://github.com/xSAMRATx/erp_sales_module.git
cd erp_sales_module

composer install
npm install

cp .env.example .env
php artisan key:generate
php artisan migrate --seed

php artisan serve   # terminal 1
npm run dev         # terminal 2
```

Open http://localhost:8000. The seeder creates an admin user (see `database/seeders/DatabaseSeeder.php`) plus 5 customers and 10 products.

## Project structure

- `app/Http/Controllers/SaleController.php` — sales list, create, edit and AJAX product lookup
- `app/Models` — `Customer`, `Product`, `Sale`, `SaleItem`, `SaleProduct`, `Note`
- `resources/views/sales` — Blade pages for the sales list, create and edit
- `database/migrations` — table definitions

## Author

**Md. Abdul Aziz Samrat** — [GitHub](https://github.com/xSAMRATx) · [LinkedIn](https://www.linkedin.com/in/md-abdul-aziz-samrat-797738206/)
