<?php

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
    return view('welcome');
});
/*
Route::group([
    'prefix' => 'admin/categories',
    'namespace' => 'Admin',
    'as' => 'admin.categories.',

], function() {
    Route::get('/', 'CategoriesController@index')->name('index');
    Route::get('/create', 'CategoriesController@create')->name('create');
    Route::post('/', 'CategoriesController@store')->name('store');
    Route::get('/{id}', 'CategoriesController@edit')->name('edit');
    Route::put('/{id}', 'CategoriesController@update')->name('update');
    Route::delete('/{id}', 'CategoriesController@delete')->name('delete');
});
*/

Route::namespace('Admin')
    ->prefix('/admin/categories')
    ->as('admin.categories.')
    ->group(function() {
        Route::get('/', 'CategoriesController@index')->name('index');
        Route::get('/create', 'CategoriesController@create')->name('create');
        Route::post('/', 'CategoriesController@store')->name('store');
        Route::get('/{id}', 'CategoriesController@edit')->name('edit')->where('id', '\d+');
        Route::put('/{id}', 'CategoriesController@update')->name('update')->where('id', '\d+');
        Route::delete('/{id}', 'CategoriesController@delete')->name('delete')->where('id', '\d+');
    });

Route::get('/storage/{file}', function($file) {
    $filename = storage_path('app/public/' . $file);
    return response()->file($filename);
})->where('file', '.+');




