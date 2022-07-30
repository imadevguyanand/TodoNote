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
    return "TodoNotes API using ===========> " . $router->app->version();
});

// Unprotected Routes
// Sign up a new user
$router->post('/sign-up', ['uses' => 'UserController@signUpUser']);

// List all the todo's for a specific user
$router->get('/user/{id}/list-all-todo', ['uses' => 'TodoController@listAllTodos']);
