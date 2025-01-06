<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\GeocodeController;



//rutaele paginilor  si metoda (get, resource etc)
Route::get("/", [HomeController::class, "index"])->name("home");
Route::get('/jobs/search', [JobController::class, 'search'])->name('jobs.search');
// route::resource("jobs", JobController::class);
// //ruta generala pentru toate resursele, acum se creaza deci foloseste auth
Route::resource('jobs', JobController::class)->middleware('auth')->only(['create', 'edit', 'update', 'destroy']);
//acum se aplica toate rutele cu exceptia,...
Route::resource('jobs', JobController::class)->except(['create', 'edit', 'update', 'destroy']);
//
//
Route::middleware('guest')->group(function () {

    Route::get("/register", [RegisterController::class, "register"])->name("register");
    // aici este ruta de stocare dupa inregistrare
    Route::post("/register", [RegisterController::class, "store"])->name("register.store");
    //aici este ruta pentru login
    Route::get("/login", [LoginController::class, "login"])->name("login")->middleware('guest');
    //aici este ruta de autentificare dupa logare
    Route::post("/login", [LoginController::class, "authenticate"])->name("login.authenticate");
});


//log-out route
Route::post("/logout", [LoginController::class, "logout"])->name("logout");
//ruta pentru dashboard
Route::get("/dashboard", [DashboardController::class, "index"])->name("dashboard")->middleware("auth");
//ruta de update profile info
Route::put("/profile", [ProfileController::class, "update"])->name("profile.update")->middleware("auth");
//ruta pentru bookmarkuri
Route::middleware('auth')->group(function () {
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/bookmarks/{job}', [BookmarkController::class, 'store'])->name('bookmarks.store');
    Route::delete('/bookmarks/{job}', [BookmarkController::class, 'destroy'])->name('bookmarks.destroy');
});
Route::post('/jobs/{job}/apply', [ApplicantController::class, 'store'])->name('applicant.store')->middleware('auth');
Route::delete('/applicants/{applicant}', [ApplicantController::class, 'destroy'])->name('applicant.destroy')->middleware('auth');

Route::get('geocode', [GeocodeController::class, 'geocode']);
