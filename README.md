# Seamless Payment Integration — Laravel Demo

## Requirements
- PHP 8.4+
- Laravel 12.37.0 (tested)
- Internet access (required to call sandbox API)

---

## Install & Run
1. **Clone this repository**
   ```bash
   git clone https://github.com/DevSunilShaw/Seamless-Payment-App.git
   cd Seamless-Payment-App
   php artisan serve


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
1.png
2.png
3.png
