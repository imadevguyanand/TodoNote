<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class UserTest extends TestCase
{
    public function testUserCanSignUp()
    {
        $user = User::factory()->make()->toArray();

        $response = $this->json('POST', '/sign-up', $user);

        $response->seeJson([
            'email' => $user['email']
        ]);
    }

    public function testUserCanSignIn()
    {
        $user = User::factory()->create();

        $hasUser = $user ? true : false;

        $this->assertTrue($hasUser);

        $response = $this->actingAs($user)->get('/api/todo');

        $response->assertEquals(Response::HTTP_OK, $this->response->status());
    }
}