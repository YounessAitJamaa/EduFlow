<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/register', function () {
    return view('auth.register');
});

Route::get('/courses', function () {
    return view('courses.index');
});

Route::get('/courses/{id}', function ($id) {
    return view('courses.show', ['id' => $id]);
});

Route::get('/dashboard', function () {
    return view('student.dashboard');
});

Route::get('/payment/success', function () {
    return view('payment.success');
});

Route::get('/payment/cancel', function () {
    return view('payment.cancel');
});
