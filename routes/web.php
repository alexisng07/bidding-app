<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Models\Product;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    // Convenience redirect straight to the first seeded product.
    $product = Product::first();
    return $product
        ? redirect()->route('product.show', $product)
        : response('No product seeded yet. Run: php artisan db:seed', 200);
});

Route::get('/products/{product}', [ProductController::class, 'show'])->name('product.show');
Route::get('/products/{product}/status', [ProductController::class, 'status'])->name('product.status');
Route::post('/products/{product}/bid', [ProductController::class, 'bid'])->name('product.bid');



