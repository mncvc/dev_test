<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ListController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\StatsController;
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

//Route::get('/', function () {
//    return view('welcome');
//});

//Route::get('/', [ListController::class, 'index'])->name('admin.index')->middleware('auth');
//Route::get('/', [ListController::class, 'first_page'])->name('admin.first');

Route::group([], function(){
    Route::match(['get','post'], '/', ['uses' => '\App\Http\Controllers\ListController@first_page', 'as' => '/']);
});

Route::get('/admin', [ListController::class, 'index'])->name('admin.index')->middleware('basic_auth');
//Route::get('/admin/create', [ListController::class, 'create'])->name('admin.test');

// 로그인
Route::get('/login', [UserController::class, 'index'])->name('admin.login');
Route::get('/logout', [UserController::class, 'logout'])->name('admin.logout');

Route::post('/login', [UserController::class, 'login'])->name('admin.login');
Route::get('/createQr', [UserController::class, 'createQr'])->name('admin.createQr');
Route::get('/otpAuth', [UserController::class, 'showOtp'])->name('admin.otpAuth');
Route::post('/otpAuth', [UserController::class, 'otpProcess'])->name('admin.otpAuth');

// 회원가입
Route::get('/signup', [UserController::class, 'signUpForm'])->name('admin.signup')->middleware('basic_auth');
Route::post('/signup', [UserController::class, 'signUpProcess'])->name('admin.signup');

Route::get('/user', [UserController::class, 'showUser'])->name('admin.user')->middleware('basic_auth');


Route::get('/member/detail', [UserController::class, 'detail'])->name('admin.detail')->middleware('basic_auth');

Route::get('/member/mypage', [UserController::class, 'myPage'])->name('admin.mypage')->middleware('basic_auth');

Route::post('/member/otpReset', [UserController::class, 'otpReset'])->name('admin.otpReset');

Route::get('/log', [LogController::class, 'index'])->name('log.index')->middleware('basic_auth');
//Route::get('/blog', function () {
//    return view('blog.index');
//});

//통계

// 통계 리스트
Route::get('/stats',[StatsController::class, 'index'])->name('stats.index')->middleware('basic_auth');


Route::get('/statsReg',[StatsController::class, 'create'])->name('stats.create')->middleware('basic_auth');

Route::post('/statsReg/t',[StatsController::class, 'setStats'])->name('stats.setStats')->middleware('basic_auth');

//통계 프로세스






