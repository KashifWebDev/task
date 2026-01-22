<?php

use App\Http\Controllers\StudentUploadController;
use Illuminate\Support\Facades\Route;

Route::post('/uploads/students', [StudentUploadController::class, 'upload']);
