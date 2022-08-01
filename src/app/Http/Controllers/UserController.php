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
use Carbon\Carbon;
use App\Http\Helper;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->helper = new Helper();
        $this->user = new CRUDRepository(new User());
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
        $input = [
            'email' => $request->email,
            'password' => $request->password
        ];

        // Basic validation rules
        $rules = [
            'email' => 'required|email:rfc,filter|unique:users',
            'password' => 'required|min:8'
        ];

        // Display message for the API consumer
        $messages = [
            'required' => 'The :attribute is required.',
            'email' => 'The :attribute is not valid',
            'unique' => 'The :attribute is already taken'
        ];

        // Check if the parameters passed are valid
        $validationResult = $this->helper->validator($input, $rules, $messages);
        
        if (count($validationResult) > 0) {
            return response()->json([
                'message' => $validationResult,
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        $userData = [
            'email' => $request->email,
            'password' => $this->helper->getHashedString($request->password), 
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ];
        
        return response()->json([
            "message" => $this->user->create($userData)
        ],Response::HTTP_CREATED);
    }

    /**
     * Sign in a user
     * 
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function signIn(Request $request) 
    {
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

        // Check if the parameters passed are valid
        $validationResult = $this->helper->validator($input, $rules, $messages);
        
        if (count($validationResult) > 0) {
            return response()->json([
                'message' => $validationResult,
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        $tokenResult = $this->helper->getToken($request->email, $request->password);

        if(isset($tokenResult['access_token'])) {
            return response()->json([
                'message' => $tokenResult
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'message' => $tokenResult
            ], Response::HTTP_UNAUTHORIZED);
        }        
    }
}
