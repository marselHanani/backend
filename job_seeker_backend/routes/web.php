<?php

use Illuminate\Support\Facades\Route;

Route::get('login',function(){
    return "i am in get http request";
});

Route::get('/', function() {
    return view('welcome'); 
   
});
