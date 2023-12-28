<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MeetController as meet;

Auth::routes();
Route::group(['middleware' => ['web', 'activity', 'checkblocked']], function () {
    Route::get('/social/redirect/{provider}', ['as' => 'social.redirect', 'uses' => 'App\Http\Controllers\Auth\SocialController@getSocialRedirect']);
    Route::get('/social/handle/{provider}', ['as' => 'social.handle', 'uses' => 'App\Http\Controllers\Auth\SocialController@getSocialHandle']); 
});
Route::group(['middleware' => ['auth', 'activity', 'checkblocked']], function () {
    Route::get('/logout', ['uses' => 'App\Http\Controllers\Auth\LoginController@logout'])->name('logout');
});
Route::get("/" , function() { return redirect("/home");})->name("index");
Route::group(['middleware' => ['auth', 'activated', 'activity', 'twostep', 'checkblocked']], function () {
    Route::get("/home" , [meet::class , "index"])->name("quickmeet");
    Route::prefix('meet')->group(function () {
        Route::get("", [meet::class, "accessMeeting"])->name("meet");
        Route::get("create", [meet::class, "createMeet"])->name("createMeet");
        Route::get("list", [meet::class, "listMeet"])->name("listMeet");
        Route::prefix('focused')->group(function () {
            Route::get("create", [meet::class, "meetFocusedCreate"])->name("meet.focused.create");
            Route::get("edit/{meeting_id}", [meet::class, "meetFocusedEdit"])->name("meet.focused.edit");
        });
    });
    Route::get("/attendance_taker", [meet::class, "attendanceTaker"])->name("attendance_taker");
});
