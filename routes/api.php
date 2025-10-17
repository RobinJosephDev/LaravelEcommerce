<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// ==========================
// 🔹 CUSTOMER CONTROLLERS
// ==========================
use App\Http\Controllers\Customer\ProductController as CustomerProductController;
use App\Http\Controllers\Customer\CartController as CustomerCartController;
use App\Http\Controllers\Customer\WishlistController as CustomerWishlistController;
use App\Http\Controllers\Customer\CheckoutController as CustomerCheckoutController;
use App\Http\Controllers\Customer\ReviewController as CustomerReviewController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;

// ==========================
// 🔹 ADMIN CONTROLLERS
// ==========================
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\RegisterController;

// ==========================
// 🔹 VENDOR CONTROLLERS
// ==========================
use App\Http\Controllers\Vendor\ProductController as VendorProductController;
use App\Http\Controllers\Vendor\OrderController as VendorOrderController;

// ==========================
// 🔹 AUTH CONTROLLERS
// ==========================
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;


// ----------------------
// 🔹 TEST ROUTE
// ----------------------
Route::get('/test', fn() => response()->json(['message' => 'API works']));

// ----------------------
// 🔹 PUBLIC CUSTOMER API ROUTES
// ----------------------
Route::get('/products', [CustomerProductController::class, 'index']);
Route::get('/products/{product}', [CustomerProductController::class, 'show']);

// ----------------------
// 🔹 AUTHENTICATION
// ----------------------
Route::post('/register', [RegisterController::class, 'store']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']);
Route::post('/reset-password', [NewPasswordController::class, 'store']);

// ----------------------
// 🔹 PROTECTED API ROUTES (Sanctum)
// ----------------------
Route::middleware('auth:sanctum')->group(function () {

    // Logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);

    // ==========================
    // 🔹 CUSTOMER PROFILE (API VERSION)
    // ==========================
    Route::get('/profile', function (Request $request) {
        return response()->json([
            'user' => $request->user(),
        ]);
    });

    Route::patch('/profile', function (Request $request) {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->fresh(),
        ]);
    });

    Route::delete('/profile', function (Request $request) {
        $user = $request->user();
        $user->delete();

        return response()->json(['message' => 'Account deleted successfully']);
    });

    // ==========================
    // 🔹 CUSTOMER CART
    // ==========================
    Route::get('/cart', [CustomerCartController::class, 'index']);
    Route::post('/cart/add/{product}', [CustomerCartController::class, 'add']);
    Route::post('/cart/remove/{item}', [CustomerCartController::class, 'remove']);
    Route::post('/cart/update/{item}/{quantity}', [CustomerCartController::class, 'update']);

    // ==========================
    // 🔹 CUSTOMER WISHLIST
    // ==========================
    Route::get('/wishlist', [CustomerWishlistController::class, 'index']);
    Route::post('/wishlist/toggle/{product}', [CustomerWishlistController::class, 'toggle']);

    // ==========================
    // 🔹 CUSTOMER CHECKOUT
    // ==========================
    Route::get('/checkout', [CustomerCheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CustomerCheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success/{order}', [CustomerCheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel/{order?}', [CustomerCheckoutController::class, 'cancel'])->name('checkout.cancel');


    // ==========================
    // 🔹 CUSTOMER REVIEWS
    // ==========================
    Route::post('/products/{product}/review', [CustomerReviewController::class, 'store']);

    // ==========================
    // 🔹 ADMIN API ROUTES
    // ==========================
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', fn() => response()->json(['message' => 'Admin Dashboard']));
        Route::apiResource('/products', AdminProductController::class);
        Route::get('/orders', [AdminOrderController::class, 'index']);
        Route::get('/orders/{order}', [AdminOrderController::class, 'show']);
        Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus']);
    });

    // ==========================
    // 🔹 VENDOR API ROUTES
    // ==========================
    Route::prefix('vendor')->group(function () {
        Route::get('/dashboard', fn() => response()->json(['message' => 'Vendor Dashboard']));
        Route::apiResource('/products', VendorProductController::class);
        Route::get('/orders', [VendorOrderController::class, 'index']);
        Route::get('/orders/{order}', [VendorOrderController::class, 'show']);
    });
});
