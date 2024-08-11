<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/users', [UserController::class, 'create']);
Route::get('/users', [UserController::class, 'show']);
Route::get('/users/{id}', [UserController::class, 'detail']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

Route::post('/posts', [PostController::class, 'create']);
Route::get('/posts/{id}', [PostController::class, 'show']);
Route::put('/posts/{id}', [PostController::class, 'update']);
Route::delete('/posts/{id}', [PostController::class, 'destroy']);
Route::post('/posts/{id}/like', [PostController::class, 'like']);

Route::post('/comments', [CommentController::class, 'create']);
Route::get('/posts/{postId}/comments', [CommentController::class, 'getCommentsForPost']);
