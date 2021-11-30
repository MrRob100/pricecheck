<?php

use App\Http\Controllers\PairsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    dump(json_decode(file_get_contents(public_path() . '/pairs.json')));

    dump(shell_exec('php artisan route:list'));

    return view('welcome');
});

Route::post('sync', [PairsController::class, 'update']);

Route::get('sync', [PairsController::class, 'get']);
