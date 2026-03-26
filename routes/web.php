<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

// ─── Institutional / Public Pages ───────────────────────────────────
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/funcionalidades', [PageController::class, 'features'])->name('features');
Route::get('/beneficios', [PageController::class, 'benefits'])->name('benefits');
Route::get('/depoimentos', [PageController::class, 'testimonials'])->name('testimonials');
Route::get('/sobre', [PageController::class, 'about'])->name('about');
Route::get('/termos', [PageController::class, 'terms'])->name('terms');
Route::get('/privacidade', [PageController::class, 'privacy'])->name('privacy');

// Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post}', [BlogController::class, 'show'])->name('blog.show');

// Contact
Route::get('/contato', [ContactController::class, 'index'])->name('contact');
Route::post('/contato', [ContactController::class, 'send'])->name('contact.send');

// Auth (guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Accounts
    Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::get('/accounts/create', [AccountController::class, 'create'])->name('accounts.create');
    Route::post('/accounts', [AccountController::class, 'store'])->name('accounts.store');

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::post('/transactions/delete', [TransactionController::class, 'destroy'])->name('transactions.destroy');

    // Categories (AJAX)
    Route::get('/categories/by-type', [TransactionController::class, 'categoriesByType'])->name('categories.byType');

    // Budgets
    Route::get('/budgets', [BudgetController::class, 'index'])->name('budgets.index');
    Route::get('/budgets/create', [BudgetController::class, 'create'])->name('budgets.create');
    Route::post('/budgets', [BudgetController::class, 'store'])->name('budgets.store');
    Route::post('/budgets/delete', [BudgetController::class, 'destroy'])->name('budgets.destroy');

    // Goals
    Route::get('/goals', [GoalController::class, 'index'])->name('goals.index');
    Route::get('/goals/create', [GoalController::class, 'create'])->name('goals.create');
    Route::post('/goals', [GoalController::class, 'store'])->name('goals.store');
    Route::get('/goals/show', [GoalController::class, 'show'])->name('goals.show');
    Route::post('/goals/contribute', [GoalController::class, 'contribute'])->name('goals.contribute');
    Route::post('/goals/cancel', [GoalController::class, 'cancel'])->name('goals.cancel');

    // Debts
    Route::get('/debts', [DebtController::class, 'index'])->name('debts.index');
    Route::get('/debts/create', [DebtController::class, 'create'])->name('debts.create');
    Route::post('/debts', [DebtController::class, 'store'])->name('debts.store');
    Route::post('/debts/pay', [DebtController::class, 'pay'])->name('debts.pay');

    // Investments
    Route::get('/investments', [InvestmentController::class, 'index'])->name('investments.index');
    Route::get('/investments/create', [InvestmentController::class, 'create'])->name('investments.create');
    Route::post('/investments', [InvestmentController::class, 'store'])->name('investments.store');
    Route::post('/investments/delete', [InvestmentController::class, 'destroy'])->name('investments.destroy');
    Route::get('/investments/assets-by-type', [InvestmentController::class, 'assetsByType'])->name('investments.assetsByType');
});
