<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\Api\ProdukController;

Route::apiResource('produk', ProdukController::class);
