<?php

use Illuminate\Support\Facades\Route;



Route::namespace('App\Http\Controllers')->group(function(){



    //Login
    Route::namespace('Auth')->group(function(){
        Route::get('/login', 'IndexController@index');
        Route::post('/login_action', 'IndexController@login_action');
        Route::get('/logout', 'IndexController@logout');
    });

    // cost
    Route::prefix('cost')->group(function(){
        Route::get('/index', 'CostController@index');
        Route::post('/store', 'CostController@store')->middleware('auth');
        Route::post('/update/{id}', 'CostController@update')->middleware('auth');
        Route::get('/edit/{id}', 'CostController@edit')->middleware('auth');
        Route::post('/status/{id}', 'CostController@status');
        Route::delete('/destroy/{id}', 'CostController@destroy')->middleware('auth');
        Route::delete('/destroy_temp/{id}', 'CostController@destroy_temp')->middleware('auth');

        Route::get('/resend_line/{id}', 'CostController@resend_line')->middleware('auth');
    });

    // crypto
    Route::prefix('crypto')->group(function(){
        Route::get('/index', 'CryptoController@index');
        Route::get('/options', 'CryptoController@getCryptoOptions');
        Route::post('/store', 'CryptoController@store')->middleware('auth');
        Route::post('/update/{id}', 'CryptoController@update')->middleware('auth');
        Route::get('/edit/{id}', 'CryptoController@edit')->middleware('auth');
        Route::post('/status/{id}', 'CryptoController@status');
        Route::delete('/destroy/{id}', 'CryptoController@destroy')->middleware('auth');
        Route::delete('/destroy_temp/{id}', 'CryptoController@destroy_temp')->middleware('auth');

    });

    // deposit
    Route::prefix('deposit')->group(function(){
        Route::get('/index', 'DepositController@index');
        Route::post('/store', 'DepositController@store')->middleware('auth');
        Route::post('/update/{id}', 'DepositController@update')->middleware('auth');
        Route::get('/edit/{id}', 'DepositController@edit')->middleware('auth');
        Route::post('/status/{id}', 'DepositController@status');
        Route::delete('/destroy/{id}', 'DepositController@destroy')->middleware('auth');
        Route::delete('/destroy_temp/{id}', 'DepositController@destroy_temp')->middleware('auth');

        Route::get('/resend_line/{id}', 'DepositController@resend_line')->middleware('auth');
    });




    // ExpInc
    Route::namespace('ExpInc')->middleware('auth')->group(function(){


        // income
        Route::prefix('income')->group(function(){
            Route::get('/index', 'IncomeController@index');
            Route::post('/store', 'IncomeController@store');
            Route::post('/update/{id}', 'IncomeController@update');
            Route::get('/edit/{id}', 'IncomeController@edit');
            Route::post('/status/{id}', 'IncomeController@status');
            Route::delete('/destroy/{id}', 'IncomeController@destroy');
            Route::delete('/destroy_temp/{id}', 'IncomeController@destroy_temp');
        });

        // expense_type
        Route::prefix('expense_type')->group(function(){
            Route::get('/index', 'TypeController@index');
            Route::post('/store', 'TypeController@store');
            Route::post('/update/{id}', 'TypeController@update');
            Route::get('/edit/{id}', 'TypeController@edit');
            Route::post('/status/{id}', 'TypeController@status');
            Route::delete('/destroy/{id}', 'TypeController@destroy');
            Route::delete('/destroy_temp/{id}', 'TypeController@destroy_temp');
        });

         // expense
         Route::prefix('expense')->group(function(){
            Route::get('/index', 'ExpenseController@index');
            Route::post('/store', 'ExpenseController@store');
            Route::post('/update/{id}', 'ExpenseController@update');
            Route::get('/edit/{id}', 'ExpenseController@edit');
            Route::post('/status/{id}', 'ExpenseController@status');
            Route::delete('/destroy/{id}', 'ExpenseController@destroy');
            Route::delete('/destroy_temp/{id}', 'ExpenseController@destroy_temp');
        });

    });

    Route::get('{any?}', 'IndexController@index')->name('dashboard');
});
