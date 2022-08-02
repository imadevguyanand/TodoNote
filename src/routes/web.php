<?php

use Laravel\Lumen\Routing\Router;

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
    return $router->app->version();
});

// Unprotected Routes
// Sign up a new user
$router->post('/sign-up', ['uses' => 'UserController@signUpUser']);

// Sign In a user
$router->post('/sign-in', ['uses' => 'UserController@signIn']);

// List all the todo's for a specific user
$router->get('/user/{id}/list-all-todo', ['uses' => 'TodoController@listByUserId']);

// Protected middlewares
$router->group(['prefix' => 'api', 'middleware' => 'auth'], function () use ($router) {

    $router->post('/todo', ['as' => 'create_todo', 'uses' => 'TodoController@store']);

    $router->delete('/todo/{id}', ['as' => 'delete_todo', 'uses' => 'TodoController@delete']);

    $router->put('/todo/{id}', ['as' => 'update_todo', 'uses' => 'TodoController@update']);

    $router->get('/todo', ['as' => 'fetch_all_todos', 'uses' => 'TodoController@list']);
});