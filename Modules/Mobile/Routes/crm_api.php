<?php

use Illuminate\Routing\Route;

Route::group(['prefix' => 'crm'], function () {
    // tasks
    Route::group(['prefix' => 'tasks'], function () {
        Route::get('/', 'TaskController@index');
        Route::post('/add', 'TaskController@store');
        Route::post('/update', 'TaskController@update');
        Route::delete('/delete', 'TaskController@destroy');
    });
});
