# Laravel eCommerce For Varsity Project

Laravel-based eCommerce application with product browsing, cart, wishlist, checkout, order tracking, admin management, blog features, and PayPal integration.

## Tech Stack

- PHP 8.1+
- Laravel 10
- MySQL
- Blade
- Bootstrap 4
- jQuery
- Vue 2
- Laravel Mix

## Features

- Product grid and list views
- Category, subcategory, and brand filtering
- Cart and wishlist
- Checkout and order placement
- Order tracking
- Admin dashboard for products, orders, coupons, banners, posts, and users
- Product reviews and blog comments
- PayPal and cash on delivery payment options

## Requirements

- PHP >= 8.1
- Composer
- Node.js and npm
- MySQL or MariaDB

## Installation

1. Clone the repository:

```bash
git clone https://github.com/imtiajsultan1/Laravel_eCommerce_For_Varsity_Project.git
cd Laravel_eCommerce_For_Varsity_Project
```

2. Install dependencies:

```bash
composer install
npm install
```

3. Create the environment file:

```bash
copy .env.example .env
php artisan key:generate
```

4. Update `.env` with your database and app settings.

Example:

```env
APP_NAME="DIU SHOP"
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=eshop
DB_USERNAME=root
DB_PASSWORD=
```

5. Set up the database:

Option A: run migrations and seeders

```bash
php artisan migrate --seed
```

Option B: import the provided SQL dump:

- Import `database/e-shop.sql` into your MySQL database

6. Create the storage symlink:

```bash
php artisan storage:link
```

7. Build frontend assets:

```bash
npm run dev
```

8. Run the application:

```bash
php artisan serve
```

Open `http://127.0.0.1:8000`

## Default Admin Login

- Email: `admin@gmail.com`
- Password: `1111`

## Repository

- GitHub: https://github.com/imtiajsultan1/Laravel_eCommerce_For_Varsity_Project

## Notes

- `.env`, `vendor`, and `node_modules` are not committed.
- PayPal credentials must be added in `.env` before using PayPal checkout.
- The project currently displays currency as `TK` in the UI.

## License

This project is licensed under the MIT License.
