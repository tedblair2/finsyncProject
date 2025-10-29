<?php

use App\Http\Controllers\BanksController;
use App\Http\Controllers\NCBAController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::post('/oauth/v1/banks/generate-access-token',[BanksController::class,'generateAccessToken']);

Route::middleware(['auth:sanctum','ability:other'])->group(function () {
    Route::post('/bank',[BanksController::class,'createBankDetails']);
});

Route::post('/transactions/v1/ncba/production',[NCBAController::class,'productionNCBANotification']);
Route::post('/transactions/v1/ncba/sandbox',[NCBAController::class,'sanboxNCBANotification']);

Route::get('/transactions',[BanksController::class,'getTransactions']);
Route::get('/transactions/export',[BanksController::class,'exportTransactions']);

Route::middleware(['auth:sanctum','ability:bank-oauth'])->group(function () {
    
    Route::get('/test',function(){
        $bank=Auth::user();
        return response()->json([
            'data'=>'Authorized token',
            'bank'=>$bank
        ]);
    });
});

// Route::post('/oauth/v1/banks/generate-new-key',[BanksController::class,'generateNewBankKey']);





