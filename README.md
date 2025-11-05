# Seamless Payment Integration — Laravel Demo

## Requirements
- PHP 8.4
- Composer
- Laravel 12.37.0 (tested)
- Internet access (to call sandbox API)

## Install
1. Clone repo
2. `composer install`
3. Copy `.env.example` to `.env` and set `APP_URL` if you want
4. `php artisan key:generate`
5. Start server: `php artisan serve` (default http://127.0.0.1:8000)

## What this implements
- Create Transaction -> calls `https://sandboxtest.space/en/purchase/create-transaction`
- Get UPI Deposit Details -> calls `https://sandboxtest.space/en/purchase/get-deposit-details`
- Validate Transaction -> calls `https://sandboxtest.space/api/v1/validate-transaction`
- CSRF: included in blade and JS
- Logging: all API requests/responses are logged to storage/logs/laravel.log

## Notes
- The sandbox sometimes returns an array-wrapped JSON — controller normalizes it.
- For local dev the controller uses `Http::withoutVerifying()` to avoid SSL issues.
- All newly created transactions return `Pending` per assignment rules.
