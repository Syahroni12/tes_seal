<?php

use App\Http\Controllers\ProjeckController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [UserController::class, 'registrasi']);
Route::post('/login', [UserController::class, 'login']);

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/tambah_user', [UserController::class, 'tambah_user'])->middleware('admin');
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/user', [UserController::class, 'index'])->middleware('admin');
    Route::get('/user/{id}', [UserController::class, 'show'])->middleware('admin');
    Route::put('/user/{id}', [UserController::class, 'update'])->middleware('admin');
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->middleware('admin');

    Route::apiResource('projects', ProjeckController::class)->middleware('admin');
    Route::apiResource('tasks', TaskController::class)->middleware('admin');

    Route::get('/task_saya/{id_user}', [TaskController::class, 'task_saya']);
    Route::put('/ubah_tasksaya/{id_task}', [TaskController::class, 'ubah_tasksaya']);




    // Route::post()
});
