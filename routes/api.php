<?php

use Illuminate\Support\Facades\Route; // <-- Add this line
use App\Http\Controllers\PurchaseRequisitionController;

Route::apiResource('purchase-requisitions', PurchaseRequisitionController::class);
