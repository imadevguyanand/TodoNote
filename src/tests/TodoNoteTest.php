<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\TodoNote;
use Symfony\Component\HttpFoundation\Response;

class TodoTest extends TestCase
{
    public function test_list_todo()
    {
        $user = $this->getTestUser();

        $response = $this->actingAs($user)->get('/api/todo');

        $response->assertEquals(Response::HTTP_OK, $this->response->status());
    }

    public function test_create_todo()
    {
        $user = $this->getTestUser();
        $content = TodoNote::factory()->make()->toArray();

        $response = $this->actingAs($user)->post('/api/todo', array_merge($content, ['user_id' => $user->id]));

        $response->assertEquals(Response::HTTP_CREATED, $this->response->status());
    }

    public function test_delete_todo()
    {
        $user = $this->getTestUser();
        $todo = $this->getTestUserTodo();

        $response = $this->actingAs($user)->delete('/api/todo/' . $todo->id);

        $response->assertEquals(Response::HTTP_OK, $this->response->status());
    }

    public function test_mark_incomplete()
    {
        $user = $this->getTestUser();

        $todo = $this->getTestUserTodo(true);

        $response = $this->actingAs($user)->put('/api/todo/' . $todo->id, ['completed' => false]);

        $response->assertEquals(Response::HTTP_OK, $this->response->status());
    }

    public function test_mark_complete()
    {
        $user = $this->getTestUser();
        $todo = $this->getTestUserTodo();

        $response = $this->actingAs($user)->put('/api/todo/' . $todo->id, ['completed' => true]);

        $response->assertEquals(Response::HTTP_OK, $this->response->status());
    }

    public function test_list_todo_by_user()
    {
        $response = $this->call('GET', '/user/' . User::TEST_USER_ID . '/list-all-todo');

        $this->assertEquals(Response::HTTP_OK, $response->status());
    }

    public function test_can_not_list_todo_for_invalid_user()
    {
        $response = $this->call('GET', '/user/100000/list-all-todo');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->status());
    }

    public function test_can_not_create_todo_with_out_content()
    {
        $user = $this->getTestUser();
        $content = TodoNote::factory()->make(['content' => ''])->toArray();

        $response = $this->actingAs($user)->post('/api/todo', array_merge($content, ['user_id' => $user->id]));

        $response->assertEquals(Response::HTTP_NOT_ACCEPTABLE, $this->response->status());
    }

    public function test_un_authorized_user_can_not_create_todo()
    {
        $user = $this->getTestUser();
        $content = TodoNote::factory()->make()->toArray();

        $response = $this->post('/api/todo', array_merge($content, ['user_id' => $user->id]));

        $response->assertEquals(Response::HTTP_UNAUTHORIZED, $this->response->status());
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