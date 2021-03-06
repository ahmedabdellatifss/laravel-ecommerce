<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LoginController;
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

define('PAGINATION_COUNT' , 10 );  // #09

Route::group([ 'namespace'=>'Admin' , 'middleware' =>'auth:admin'] , function () {

        Route::get('/' , 'DashboardController@index')->name('admin.dashboard');

        ##################### Begin languages Route ################################
        Route::group(['prefix'=>'languages'] , function(){

            Route::get('/' , 'LanguagesController@index')->name('admin.languages');
            Route::get('create' , 'LanguagesController@create')->name('admin.languages.create');
            Route::post('store' , 'LanguagesController@store')->name('admin.languages.store');

            Route::get('edit/{id}' , 'LanguagesController@edit')->name('admin.languages.edit');
            Route::post('update/{id}' , 'LanguagesController@update')->name('admin.languages.update');

            Route::get('delete/{id}' , 'LanguagesController@destroy')->name('admin.languages.delete');
        });


    ##################### Begin Main Categories Route ################################

    Route::group(['prefix'=>'main_categories'] , function(){

        Route::get('/' , 'MainCategoriesController@index')->name('admin.maincategories');
        Route::get('create' , 'MainCategoriesController@create')->name('admin.maincategories.create');
        Route::post('store' , 'MainCategoriesController@store')->name('admin.maincategories.store');

        Route::get('edit/{id}' , 'MainCategoriesController@edit')->name('admin.maincategories.edit');
        Route::post('update/{id}' , 'MainCategoriesController@update')->name('admin.maincategories.update');

        Route::get('delete/{id}' , 'MainCategoriesController@destroy')->name('admin.maincategories.delete');

        Route::get('changeStatus/{id}' , 'MainCategoriesController@changeStatus')->name('admin.maincategories.status');
        });


    ##################### End Main Categories Route ################################


    ##################### Begin Sub Categories Route ################################

    Route::group(['prefix'=>'sub_categories'] , function(){

        Route::get('/' , 'SubCategoriesController@index')->name('admin.subcategories');
        Route::get('create' , 'SubCategoriesController@create')->name('admin.subcategories.create');
        Route::post('store' , 'SubCategoriesController@store')->name('admin.subcategories.store');

        Route::get('edit/{id}' , 'SubCategoriesController@edit')->name('admin.subcategories.edit');
        Route::post('update/{id}' , 'SubCategoriesController@update')->name('admin.subcategories.update');

        Route::get('delete/{id}' , 'SubCategoriesController@destroy')->name('admin.subcategories.delete');

        Route::get('changeStatus/{id}' , 'SubCategoriesController@changeStatus')->name('admin.subcategories.status');
        });


    ##################### End Sub Categories Route ################################



    ##################### Begin Main Vendors Route ################################

    Route::group(['prefix'=>'vendors'] , function(){

        Route::get('/' , 'VendorsController@index')->name('admin.vendors');
        Route::get('create' , 'VendorsController@create')->name('admin.vendors.create');
        Route::post('store','VendorsController@store') -> name('admin.vendors.store');

        Route::get('edit/{id}' , 'VendorsController@edit')->name('admin.vendors.edit');
        Route::post('update/{id}' , 'VendorsController@update')->name('admin.vendors.update');

        Route::get('delete/{id}' , 'VendorsController@destroy')->name('admin.vendors.delete');

        Route::get('changeStatus/{id}' , 'VendorsController@changeStatus')->name('admin.vendors.status');
        });


    ##################### End Main Vendors Route ################################

});



                                                //guest mean any one can vist this rout #05
Route::group([ 'namespace'=>'Admin' , 'middleware' =>'guest:admin'] , function () {

    Route::get('login' , 'LoginController@getlogin')->name('get.admin.login');
    Route::post('login' , 'LoginController@login')->name('admin.login');

});


Route::get('test-helper' , function () {
    return show_name();
});


Route::get('subcategory' , function () {
                                            // #54
    $maincategory = \App\Models\MainCategory::find(5);

    return $maincategory->subCategories;
});
Route::get('maincategory' , function () {
                                            // #54
    $subcategory = \App\Models\SubCategory::find(1);

    return $subcategory->maincategory;
});
