<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    public function test_user_can_signup()
    {
        $user = User::factory()->make()->toArray();
        $user = array_merge($user, ['password' => Hash::make('complexpassword')]);
    
        $response = $this->json('POST', '/sign-up', $user);
        $response->seeJson([
            'email' => $user['email']
        ]);
    }

    public function test_user_can_not_sign_up_without_email()
    {
        $user = User::factory()->make(['email' => ''])->toArray();

        $response = $this->json('POST', '/sign-up', $user);
        $response->assertEquals(Response::HTTP_NOT_ACCEPTABLE, $this->response->status());
    }

    public function test_user_can_not_sign_up_without_password()
    {
        $user = User::factory()->make()->toArray();
    
        $response = $this->json('POST', '/sign-up', $user);
        $response->assertEquals(Response::HTTP_NOT_ACCEPTABLE, $this->response->status());
    }

    public function test_user_can_sign_in()
    {
        $user = User::factory()->create();

        $hasUser = $user ? true : false;
        $this->assertTrue($hasUser);
        $response = $this->actingAs($user)->get('/api/todo');
        $response->assertEquals(Response::HTTP_OK, $this->response->status());
    }

    public function test_that_base_endpoint_returns_a_successful_response()
    {
        $this->get('/');

        $this->assertEquals(
            $this->app->version(), $this->response->getContent()
        );
    }
}