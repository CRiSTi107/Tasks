<?php

define("API_VERSION", 'v1');
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version() . ' - ' . 'Current API version: ' . API_VERSION;
});

/** CORS */
$router->options(
    '/{any:.*}', [
    'middleware' => ['cors'],
    function () {
        return response('OK', 200);
    }
]);

/** Routes that doesn't require auth */
$router->group(['namespace' => API_VERSION, 'prefix' => API_VERSION, 'middleware' => 'cors'], function () use ($router) {
    $router->post('/login', ['uses' => 'UserController@login']);
    $router->post('/register', ['uses' => 'UserController@register']);
});

/** Routes with auth */
$router->group(['namespace' => API_VERSION, 'prefix' => API_VERSION, 'middleware' => 'cors|jwt'], function () use ($router) {
    // token - must be provided in request
    $router->post('/approve', ['uses' => 'UserController@approve']); // [Admin required] Route for approving users.
    //$router->post('/reset', ['uses' => 'UserController@resetPassword']); // Method moved to changePassword under /settings route.
    $router->post('/settings', ['uses' => 'UserController@settings']); // Route designed for editing own information.
    $router->post('/admin', ['uses' => 'UserController@admin']); // [Admin required] Route designed to edit every user's information.
    $router->post('/promote', ['uses' => 'UserController@promote']); // [Admin required] Promote a user to admin.
    $router->post('/demote', ['uses' => 'UserController@demote']); // [Admin required] Demote an admin to user.
});
