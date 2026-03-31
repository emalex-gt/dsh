<?php

use App\Http\Controllers\ClientBriefController;
use App\Http\Controllers\ClientBudgetController;
use App\Http\Controllers\ClientDevelopmentController;
use App\Http\Controllers\ClientDevelopmentRequestController;
use App\Http\Controllers\ClientDemoController;
use App\Http\Controllers\ClientInvoiceController;
use App\Http\Controllers\ClientTicketController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/brief', [ClientBriefController::class, 'edit'])->name('brief.edit');
Route::post('/brief', [ClientBriefController::class, 'update'])->name('brief.update');
Route::get('/brief/gracias', [ClientBriefController::class, 'thanks'])->name('brief.thanks');

Route::middleware('auth')->group(function () {
    Route::get('/mis-datos', [ClientBriefController::class, 'showClientData'])->name('client.data.show');
    Route::get('/mi-brief', [ClientBriefController::class, 'show'])->name('client.brief.show');
    Route::get('/demo-proyecto', [ClientDemoController::class, 'index'])->name('client.demos.index');
    Route::get('/desarrollo/edit-area', [ClientDevelopmentRequestController::class, 'editAreaIndex'])->name('client.development.requests.index');
    Route::get('/desarrollo/edit-area/nuevo', [ClientDevelopmentRequestController::class, 'create'])->name('client.development.requests.create');
    Route::post('/desarrollo/edit-area', [ClientDevelopmentRequestController::class, 'store'])->name('client.development.requests.store');
    Route::get('/desarrollo/edit-area/{developmentRequest}', [ClientDevelopmentRequestController::class, 'show'])->name('client.development.requests.show');
    Route::post('/desarrollo/edit-area/{developmentRequest}/responder', [ClientDevelopmentRequestController::class, 'reply'])->name('client.development.requests.reply');
    Route::get('/desarrollo/ready-area', [ClientDevelopmentRequestController::class, 'readyAreaIndex'])->name('client.development.ready.index');
    Route::get('/desarrollo/{section?}', [ClientDevelopmentController::class, 'show'])->name('client.development.show');
    Route::get('/facturacion', [ClientBudgetController::class, 'index'])->name('client.budgets.index');
    Route::get('/facturacion/{budget}', [ClientBudgetController::class, 'show'])->name('client.budgets.show');
    Route::get('/facturacion/{budget}/pdf', [ClientBudgetController::class, 'file'])->name('client.budgets.file');
    Route::post('/facturacion/{budget}/aceptar', [ClientBudgetController::class, 'accept'])->name('client.budgets.accept');
    Route::post('/facturacion/{budget}/solicitar-cambios', [ClientBudgetController::class, 'requestChanges'])->name('client.budgets.request-changes');
    Route::get('/facturacion/facturas/{invoice}', [ClientInvoiceController::class, 'show'])->name('client.invoices.show');
    Route::get('/facturacion/facturas/{invoice}/pdf', [ClientInvoiceController::class, 'file'])->name('client.invoices.file');
    Route::get('/soporte', [ClientTicketController::class, 'index'])->name('client.tickets.index');
    Route::get('/soporte/nuevo', [ClientTicketController::class, 'create'])->name('client.tickets.create');
    Route::post('/soporte', [ClientTicketController::class, 'store'])->name('client.tickets.store');
    Route::get('/soporte/{ticket}', [ClientTicketController::class, 'show'])->name('client.tickets.show');
    Route::post('/soporte/{ticket}/responder', [ClientTicketController::class, 'reply'])->name('client.tickets.reply');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
