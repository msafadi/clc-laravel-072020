<?php

use App\Http\Middleware\AuthType;
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

Route::get('/', 'HomeController@index')->name('home');
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

Route::prefix('admin')
    ->middleware(['auth', 'auth.type:admin,super-admin,user'])
    ->namespace('Admin')
    ->as('admin.')
    ->group(function() {
        Route::resource('roles', 'RolesController');

        Route::prefix('categories')
            ->as('categories.')
            ->group(function() {
                Route::get('/', 'CategoriesController@index')->name('index');
                Route::get('/create', 'CategoriesController@create')->name('create');
                Route::post('/', 'CategoriesController@store')->name('store');
                Route::get('/{category}', 'CategoriesController@edit')->name('edit')->where('id', '\d+');
                Route::put('/{id}', 'CategoriesController@update')->name('update')->where('id', '\d+');
                Route::delete('/{id}', 'CategoriesController@delete')->name('delete')->where('id', '\d+');
                Route::get('/{category}/products', 'CategoriesController@products')->name('products');
            });

        Route::get('products/trash', 'ProductsController@trash')->name('products.trash');
        //Route::delete('products/{id}/force-delete', 'ProductsController@forceDelete')->name('products.forceDelete');
        Route::put('products/{id}/restore', 'ProductsController@restore')->name('products.restore');
        Route::resource('products', 'ProductsController');
    });

Route::get('/storage/{file}', function($file) {
    $filename = storage_path('app/public/' . $file);
    return response()->file($filename);
})->where('file', '.+');





Auth::routes([
    'verify' => true,
]);

Route::get('/logout', 'Auth\LoginController@weblogout')->name('user-logout');

Route::get('admin/login', 'Admin\Auth\LoginController@showLoginForm');
Route::post('admin/login', 'Admin\Auth\LoginController@login');
Route::get('admin/password/reset', 'Admin\Auth\ForgotPasswordController@showSendEmailForm');
Route::post('admin/password/reset', 'Admin\Auth\ForgotPasswordController@sendEmail');

//Route::get('/home', 'HomeController@index')->name('home');

Route::middleware('auth')->group(function() {
    Route::get('cart', 'CartController@index')->name('cart.index');
    Route::post('cart', 'CartController@store')->name('cart.store');
    Route::delete('cart/{product_id}', 'CartController@destroy')->name('cart.destroy');

    Route::get('orders', 'OrdersController@index')->name('orders');
    Route::get('checkout', 'OrdersController@checkout')->name('checkout');
});
