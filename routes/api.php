<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth routes
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);


Route::get('/customer/list-all', [CustomerController::class, 'listCustomers']);
Route::post('/customer/create', [CustomerController::class, 'createCustomer']);
Route::get('/customer/search', [CustomerController::class, 'search']);
Route::put('/customer/edit/{id}', [CustomerController::class, 'editCustomer']);
Route::delete('/customer/delete/{id}', [CustomerController::class, 'deleteCustomer']);

Route::post('/quotation/upload', [FileUploadController::class, 'saveQuotationFile']);
Route::get('/quotation/list-all', [FileUploadController::class, 'listQuotations']);
Route::get('/quotation/search', [FileUploadController::class, 'listQuotations']);
Route::delete('/quotation/delete/{id}', [FileUploadController::class, 'deleteQuotation']);


Route::post('/quotation/read', [FileUploadController::class, 'readQuotationFiles']);
Route::get('/quotation/details', [QuotationController::class, 'detail']);
Route::get('/quotation/{id}', [FileUploadController::class, 'readQuotationById']);

// Item Pricing APIs
Route::get('/item/{itemId}/pricing', [FileUploadController::class, 'getItemPricing']);
Route::post('/item/{itemId}/pricing', [FileUploadController::class, 'updateItemPricing']);
Route::put('/item/{itemId}/vat-percentage', [FileUploadController::class, 'updateVATPercentage']);

// Send quotation to generation API
Route::post('/quotation/{id}/generate', [FileUploadController::class, 'sendQuotationToApi']);