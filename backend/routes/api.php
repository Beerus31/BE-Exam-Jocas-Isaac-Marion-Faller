<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dash\HomeController;
use App\Http\Controllers\Auth\AuthenticationController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/


/*Route::get('/', function(){
    return response()->json(['status' => true]);
});*/
Route::post('/login', [AuthenticationController::class, 'login']);

route::post('Test', [HomeController::class, 'testMethod']);
Route::post('products', [HomeController::class, 'index']);
Route::post('createtest', [HomeController::class, 'create']);
Route::get('createtest/{id}', [HomeController::class, 'show']);
Route::get('createtest/{id}/edit', [HomeController::class, 'edit']);
Route::post('createtest/{id}/edit', [HomeController::class, 'update']);
Route::delete('products/{id}', [HomeController::class, 'destroy']);
//Route::get ('create')

