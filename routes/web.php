<?php

Route::get('/code/{link}', 'CouponController@showCode');

Route::get('/{any?}', function (){
    return view('welcome');
})->where('any', '^(?!api\/)[\/\w\.-]*');

