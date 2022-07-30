<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Eloquent\Repositories\CRUDRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
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
                $this->user->create($userData), Response::HTTP_CREATED
            ]);
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
}
