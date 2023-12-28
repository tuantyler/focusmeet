<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MeetController as meet;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::middleware(['api', 'auth:web'])->group(function () {
    Route::prefix('meet')->group(function () {
        Route::prefix('update')->group(function () {
            Route::put('name', [meet::class, "updateName"])->name('meet.update.name');
        });

        Route::prefix('focused')->group(function () {
            Route::post('create', [meet::class, "createFocusedMeet"])->name('api.meet.focused.create');
        });

        Route::prefix('list')->group(function () {
            Route::get('participants/{meeting_id}', [meet::class, "listParticipants"]);
        });   
    });
});
