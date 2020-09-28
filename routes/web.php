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

Auth::routes(['register'=> false]);

Route::get('/', 'ClinicaltrialController@index')->name('home')->middleware('auth');

// Route::get('/list', function () {
//     return view('clinicaltrials.list');
// });

// Route::get('/submitting', function () {
//     return view('clinicaltrials.submitting');
// });

//trial fields
Route::group(['prefix' => 'trialfields', 'middleware' => ['auth']], function () {
    Route::get('/', ['middleware' => ['permission:list trialfields'], 'uses' => 'TrialfieldController@index']);
    Route::get('create', ['middleware' => ['permission:add trialfields'], 'uses' => 'TrialfieldController@create']);
    Route::post('/store', ['middleware' => ['permission:add trialfields'], 'uses' => 'TrialfieldController@store']);
    Route::post('/saveorder', ['middleware' => ['permission:edit trialfields'], 'uses' => 'TrialfieldController@saveorder']);
    Route::get('{id}/show', ['middleware' => ['view trialfields'], 'uses' => 'TrialfieldController@show']);
    Route::get('{id}/createoption', ['middleware' => ['permission:add trialfields'], 'uses' => 'TrialfieldController@createoption']);
    Route::get('{id}/storeoption', ['middleware' => ['permission:add trialfields'], 'uses' => 'TrialfieldController@storeoption']);
    Route::get('{id}/edit', ['middleware' => ['permission:edit trialfields'], 'uses' => 'TrialfieldController@edit']);
    Route::get('{id}/editoption', ['middleware' => ['permission:edit trialfields'], 'uses' => 'TrialfieldController@editoption']);
    Route::post('{id}/update', ['middleware' => ['permission:edit trialfields'], 'uses' => 'TrialfieldController@update']);
    Route::post('{id}/updateoption', ['middleware' => ['permission:edit trialfields'], 'uses' => 'TrialfieldController@updateoption']);
    Route::post('{id}/delete', ['middleware' => ['permission:delete trialfields'], 'uses' => 'TrialfieldController@destroy']);
});

//categories
Route::group(['prefix' => 'categories', 'middleware' => ['auth']], function () {
    Route::get('/', ['middleware' => ['permission:list categories'], 'uses' => 'CategoriesController@index'])->name('allcategory');
    Route::get('create', ['middleware' => ['permission:add categories'], 'uses' => 'CategoriesController@create']);
    Route::post('/store', ['middleware' => ['permission:add categories'], 'uses' => 'CategoriesController@store']);
    Route::post('/saveorder', ['middleware' => ['permission:edit categories'], 'uses' => 'CategoriesController@saveorder']);
    Route::get('{id}/show', ['middleware' => ['view categories'], 'uses' => 'CategoriesController@show']);
    Route::get('{id}/edit', ['middleware' => ['permission:edit categories'], 'uses' => 'CategoriesController@edit']);
    Route::post('{id}/update', ['middleware' => ['permission:edit categories'], 'uses' => 'CategoriesController@update']);
    Route::post('{id}/delete', ['middleware' => ['permission:delete categories'], 'uses' => 'CategoriesController@destroy'])->name('deletecategory');
});

//clinical trials
Route::group(['prefix' => 'clinicaltrials', 'middleware' => ['auth']], function () {
    Route::get('/', ['middleware' => ['permission:list clinicaltrials'], 'uses' => 'ClinicaltrialController@index']);
    Route::get('search', ['middleware' => ['permission:list clinicaltrials'], 'uses' => 'ClinicaltrialController@search']);
    Route::get('create', ['middleware' => ['permission:add clinicaltrials'], 'uses' => 'ClinicaltrialController@create']);
    Route::get('{id}/createbycondition', ['middleware' => ['permission:add clinicaltrials'], 'uses' => 'ClinicaltrialController@createbycondition']);
    Route::post('/store', ['middleware' => ['permission:add clinicaltrials'], 'uses' => 'ClinicaltrialController@store']);
    Route::get('sort/{id}', ['middleware' => ['permission:list clinicaltrials'], 'uses' => 'ClinicaltrialController@sort']);
    Route::get('{id}/show', ['middleware' => ['permission:view clinicaltrials'], 'uses' => 'ClinicaltrialController@show']);
    // Route::get('{id}/submitpatient', ['middleware' => ['permission:submit patient'], 'uses' => 'ClinicaltrialController@submitpatient']);
    Route::get('{id}/forwardpatient', ['middleware' => ['permission:submit patient'], 'uses' => 'ClinicaltrialController@forwardpatient']);
    Route::get('{id}/edit', ['middleware' => ['permission:edit clinicaltrials'], 'uses' => 'ClinicaltrialController@edit']);
    Route::post('{id}/update', ['middleware' => ['permission:edit clinicaltrials'], 'uses' => 'ClinicaltrialController@update']);
    Route::post('{id}/delete', ['middleware' => ['permission:delete clinicaltrials'], 'uses' => 'ClinicaltrialController@destroy']);
});

//users
Route::group(['prefix' => 'users', 'middleware' => ['auth']], function () {
    Route::get('/', ['middleware' => ['permission:list users'], 'uses' => 'UserController@index'])->name('allusers');
    Route::get('create', ['middleware' => ['permission:add users'], 'uses' => 'UserController@create']);
    Route::post('/store', ['middleware' => ['permission:add users'], 'uses' => 'UserController@store']);
    Route::get('{id}/show', ['middleware' => ['view users'], 'uses' => 'UserController@show']);
    Route::get('{id}/edit', ['middleware' => ['permission:edit users'], 'uses' => 'UserController@edit']);
    Route::post('{id}/update', ['middleware' => ['permission:edit users'], 'uses' => 'UserController@update']);
    Route::get('{id}/editprofile', ['middleware' => ['permission:edit profile'], 'uses' => 'UserController@editprofile'])->name('editprofile');
    Route::post('{id}/updateprofile', ['middleware' => ['permission:edit profile'], 'uses' => 'UserController@updateprofile']);
    Route::post('{id}/delete', ['middleware' => ['permission:delete users'], 'uses' => 'UserController@destroy'])->name('userdelete');
});
