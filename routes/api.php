<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\post;
use App\Http\Middleware\is_authenticated;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//these are the routes that are used for various purposes as the name describe
Route::post('signup',[post::class,'signup']);
Route::get('verify/{token}',[post::class,'verify_email']);
Route::post('login',[post::class,'login']);
Route::post('update/{email}',[post::class,'update']);
Route::post('forget/{email}',[post::class,'forget']);
Route::post('upload',[post::class,'upload']);
Route::post('delete',[post::class,'delete']);
Route::post('search',[post::class,'search']);
Route::post('list',[post::class,'list']);
Route::get('/Share/id/email/pass', [post::class, 'verifyimage']);
Route::get('/Share/id', [ImageController::class, 'Linkview'])->middleware(is_authenticated::class);
Route::get('/Share/id', [post::class, 'Link_view'])->middleware(is_authenticated::class);

