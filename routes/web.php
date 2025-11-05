<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

// Route::get('/', [PaymentController::class, 'index'])->name('payment.index');
Route::post('/create-transaction', [PaymentController::class, 'createTransaction'])->name('payment.create');
Route::post('/get-deposit-details', [PaymentController::class, 'getDepositDetails'])->name('payment.deposit');
Route::post('/validate-transaction', [PaymentController::class, 'validateTransaction'])->name('payment.validate');



// Dummy data for the demo
$products = [
    ['id' => 1, 'name' => 'Smartphone X1', 'price' => 799.99, 'description' => 'Latest flagship phone with 108MP camera.'],
    ['id' => 2, 'name' => 'Laptop Pro 15', 'price' => 1299.00, 'description' => 'High-performance laptop for professionals.'],
    ['id' => 3, 'name' => 'Wireless Earbuds', 'price' => 89.50, 'description' => 'Noise-cancelling earbuds with 24-hour battery life.'],
];

// Product Listing Page
Route::get('/products', function () use ($products) {
    return view('products.index', ['products' => $products]);
})->name('products.index');

// Product Details Page
Route::get('/products/{id}', function ($id) use ($products) {
    $product = collect($products)->firstWhere('id', (int)$id);

    if (!$product) {
        abort(404, 'Product not found');
    }

    return view('products.detail', ['product' => $product]);
})->name('products.show');


Route::get('/', function () {
    return redirect()->route('products.index');
});
