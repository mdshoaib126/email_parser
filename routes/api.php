<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuccessfulEmailController;
use Illuminate\Support\Facades\Crypt;

Route::post('/login', function (Request $request) {

    
    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $token = $user->createToken('API Token')->plainTextToken;

    return response()->json(['token' => $token]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/emails', [SuccessfulEmailController::class, 'store']);
    Route::get('/emails', [SuccessfulEmailController::class, 'index']);
    Route::get('/emails/{id}', [SuccessfulEmailController::class, 'show']);
    Route::put('/emails/{id}', [SuccessfulEmailController::class, 'update']);
    Route::delete('/emails/{id}', [SuccessfulEmailController::class, 'destroy']);
});
