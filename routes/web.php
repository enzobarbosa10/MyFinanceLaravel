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
use App\Http\Controllers\OpenFinanceController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ─── Institutional / Public Pages ───────────────────────────────────
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/welcome', [PageController::class, 'home']);
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

// ─── Email Verification ─────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/dashboard')->with('success', 'E-mail verificado com sucesso!');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'Link de verificação reenviado!');
    })->middleware('throttle:6,1')->name('verification.send');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Authenticated + Verified routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Planos
    Route::get('/planos', fn() => view('pages.planos'))->name('planos');

    // Accounts
    Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::get('/accounts/create', [AccountController::class, 'create'])->name('accounts.create');
    Route::post('/accounts', [AccountController::class, 'store'])->name('accounts.store');

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/{transaction}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
    Route::put('/transactions/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

    // Categories (AJAX)
    Route::get('/categories/by-type', [TransactionController::class, 'categoriesByType'])->name('categories.byType');

    // Budgets
    Route::get('/budgets', [BudgetController::class, 'index'])->name('budgets.index');
    Route::get('/budgets/create', [BudgetController::class, 'create'])->name('budgets.create');
    Route::post('/budgets', [BudgetController::class, 'store'])->name('budgets.store');
    Route::delete('/budgets/{budget}', [BudgetController::class, 'destroy'])->name('budgets.destroy');

    // Goals (premium — subscription required)
    Route::middleware('subscription')->group(function () {
        Route::get('/goals', [GoalController::class, 'index'])->name('goals.index');
        Route::get('/goals/create', [GoalController::class, 'create'])->name('goals.create');
        Route::post('/goals', [GoalController::class, 'store'])->name('goals.store');
        Route::get('/goals/{goal}', [GoalController::class, 'show'])->name('goals.show');
        Route::get('/goals/{goal}/edit', [GoalController::class, 'edit'])->name('goals.edit');
        Route::put('/goals/{goal}', [GoalController::class, 'update'])->name('goals.update');
        Route::post('/goals/{goal}/contribute', [GoalController::class, 'contribute'])->name('goals.contribute');
        Route::post('/goals/{goal}/cancel', [GoalController::class, 'cancel'])->name('goals.cancel');
    });

    // Debts
    Route::get('/debts', [DebtController::class, 'index'])->name('debts.index');
    Route::get('/debts/create', [DebtController::class, 'create'])->name('debts.create');
    Route::post('/debts', [DebtController::class, 'store'])->name('debts.store');
    Route::get('/debts/{debt}/edit', [DebtController::class, 'edit'])->name('debts.edit');
    Route::put('/debts/{debt}', [DebtController::class, 'update'])->name('debts.update');
    Route::post('/debts/pay', [DebtController::class, 'pay'])->name('debts.pay');

    // Investments (premium — subscription required)
    Route::middleware('subscription')->group(function () {
        Route::get('/investments', [InvestmentController::class, 'index'])->name('investments.index');
        Route::get('/investments/create', [InvestmentController::class, 'create'])->name('investments.create');
        Route::post('/investments', [InvestmentController::class, 'store'])->name('investments.store');
        Route::delete('/investments/{investment}', [InvestmentController::class, 'destroy'])->name('investments.destroy');
        Route::get('/investments/assets-by-type', [InvestmentController::class, 'assetsByType'])->name('investments.assetsByType');
    });

    // Open Finance
    Route::get('/open-finance', [OpenFinanceController::class, 'index'])->name('open-finance.index');
    Route::post('/open-finance/connect-token', [OpenFinanceController::class, 'connectToken'])->name('open-finance.connect-token');
});
