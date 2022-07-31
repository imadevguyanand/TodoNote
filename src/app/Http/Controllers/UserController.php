<?php

/**
 * This is the class which is reponsible to provide the API responses for the user signup and signin
 *
 * @author Anand Rajendran
 */

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Eloquent\Repositories\CRUDRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Route;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->client = DB::table('oauth_clients')->where('name', 'todo-api')->first();
        $this->user = new CRUDRepository($user);
    }

    /**
     * Sign up a new user
     * 
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function signUpUser(Request $request) 
    {
        $formData = [
            'email' => $request->email,
            'password' => $request->password
        ];

        // Basic validation rules
        $rules = [
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8'
        ];

        // Display message for the API consumer
        $messages = [
            'required' => 'The :attribute is required.',
            'email' => 'The :attribute is not valid',
            'unique' => 'The :attribute is already taken'
        ];

        $validator = Validator::make($formData, $rules, $messages);

        // Check if the parameters passed are invalid
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->messages()->all(),
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        try {
            $userData = [
                'email' => $request->email,
                // One way hashing algorithm
                'password' => Hash::make($request->password), 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
            
            return response()->json([
                "message" => $this->user->create($userData)
            ],Response::HTTP_CREATED);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'message' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function signIn(Request $request) 
    {
        global $app; 

        $input = [
            'email' => $request->email,
            'password' => $request->password
        ];

        $rules = [
            'email' => 'required|email',
            'password' => 'required'
        ];

        $messages = [
            'required' => 'The :attribute is required.',
            'email' => 'The :attribute is not valid'
        ];

        $validator = Validator::make($input, $rules, $messages);

        // Check if the parameters passed are invalid
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->messages()->all(),
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        try {
            $request = Request();

            if(!isset($this->client->id)) {
                return response()->json([
                    'message' => 'No client Id found. Please create a Password client'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $proxy = Request::create(
                '/oauth/token',
                'POST', 
                [
                    'grant_type' => 'password',
                    'client_id' => $this->client->id,
                    'client_secret' => $this->client->secret,
                    'scope' => '*',
                    'username' => $request->email,
                    'password' => $request->password
                ]
            );

            $tokenResult = $app->dispatch($proxy)->getContent();
            $tokenResult = json_decode($tokenResult, true);

            if(isset($tokenResult['access_token'])) {
                return response()->json([
                    'access_token' => $tokenResult['access_token']
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => $tokenResult
                ], Response::HTTP_UNAUTHORIZED);
            } 
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }        
    }
}
