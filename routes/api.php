<?php

use Illuminate\Http\Request;
use App\User;
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
if (env('APP_ENV') !== 'production') {
	Route::get('usersss', function() {
	    // If the Content-Type and Accept headers are set to 'application/json', 
	    // this will return a JSON structure. This will be cleaned up later.
	    $users = User::all();

	    return response()->json($users);
	});

	Route::get('userrr/{id}', function($id) {
	    $user = User::find($id);

	    return response()->json($user);
	});
}
