<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\TodoNote;
use Symfony\Component\HttpFoundation\Response;

class TodoTest extends TestCase
{
    public function testListTodo()
    {
        $user = $this->getTestUser();

        $response = $this->actingAs($user)->get('/api/todo');

        $response->assertEquals(Response::HTTP_OK, $this->response->status());
    }

    public function testCreateTodo()
    {
        $user = $this->getTestUser();
        $content = TodoNote::factory()->make()->toArray();

        $response = $this->actingAs($user)->post('/api/todo', array_merge($content, ['user_id' => $user->id]));

        $response->assertEquals(Response::HTTP_CREATED, $this->response->status());
    }

    public function testDeleteTodo()
    {
        $user = $this->getTestUser();
        $todo = $this->getTestUserTodo();

        $response = $this->actingAs($user)->delete('/api/todo/' . $todo->id);

        $response->assertEquals(Response::HTTP_OK, $this->response->status());
    }

    public function testMarkInComplete()
    {
        $user = $this->getTestUser();

        $todo = $this->getTestUserTodo(true);

        $response = $this->actingAs($user)->put('/api/todo/' . $todo->id, ['completed' => false]);

        $response->assertEquals(Response::HTTP_OK, $this->response->status());
    }

    public function testMarkComplete()
    {
        $user = $this->getTestUser();
        $todo = $this->getTestUserTodo();

        $response = $this->actingAs($user)->put('/api/todo/' . $todo->id, ['completed' => true]);

        $response->assertEquals(Response::HTTP_OK, $this->response->status());
    }

    public function testCanNotListTodoForInvalidUser()
    {
        $response = $this->call('GET', '/user/100000/list-all-todo');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->status());
    }

    public function getTestUser()
    {
        return User::find(User::TEST_USER_ID)
                ->first();
    }

    public function getTestUserTodo($completed = false)
    {
        return User::find(User::TEST_USER_ID)
                ->todonotes()
                ->when($completed === false, function ($query) {
                    return $query->where('completed_at', '=', null);
                }, function ($query) {
                    return $query->where('completed_at', '!=', null);
                })
                ->first();
    }
}