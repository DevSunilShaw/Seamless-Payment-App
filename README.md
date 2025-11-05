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

The app will be available at http://127.0.0.1:8000

Visit in browser

Open your browser and go to http://127.0.0.1:8000

![Step 1 Screenshot](1.png)

You will see the Seamless Payment Interface with the "Pay" button.

![Step 2 Screenshot](2.png)

Test the payment flow

Click “Pay” to create a test UPI transaction.

The UPI QR code and link will appear for scanning.

You can check transaction status using the “Check Status” button.

![Step 3 Screenshot](3.png)

- QR code (image or base64 render)
- A clickable UPI link (copyable)
- Payment amount and instructions

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

