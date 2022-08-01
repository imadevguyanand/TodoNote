<?php

/**
 * This is the class which is reponsible to provide the API responses for the TODO actions
 *
 * @author Anand Rajendran
 */

namespace App\Http\Controllers;

use App\Models\TodoNote;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Eloquent\Repositories\CRUDRepository;
use Carbon\Carbon;
use App\Models\User;
use App\Http\Helper;

class TodoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->helper = new Helper();
        $this->todo = new CRUDRepository(new TodoNote());
    }

    /**
     * Get all the todo's for an user
     * 
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function list(Request $request) 
    {
        // Fetch the user Id
        $userId = $request->user()->id;

        return response()->json([
            'message' => $this->getTodoListByUser($userId)
        ], Response::HTTP_OK);
    }

    /**
     * Store a Todo for an user
     * 
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function store(Request $request) 
    {
        $userId = $request->user()->id;

        $input = [
            'content' => $request->content,
            'user_id' => $userId
        ];

        $rules = [
            'content' => 'required',
            'user_id' => 'required'
        ];

        $messages = [
            'required' => 'The :attribute is required.'
        ];

        // Check if the parameters passed are valid
        $validationResult = $this->helper->validator($input, $rules, $messages);
        
        if (count($validationResult) > 0) {
            return response()->json([
                'message' => $validationResult,
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        return response()->json([
            'message' => $this->todo->create($input)
        ], Response::HTTP_CREATED);
    }

    /**
     * Update a Todo for an user
     * 
     * @param Request $request
     * @param Integer $id
     * 
     * @return JsonResponse
     */
    public function update(Request $request, $id) 
    {
        $userId = $request->user()->id;

        $isAuthorized = $this->authorizeTodoId($userId, $id);

        if($isAuthorized === false) {
            return response()->json([
                'message' => 'Invalid Todo ID: ' . $id,
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        $data = [
            'completed_at' => $this->helper->isBool($request->completed) === true ? Carbon::now() : null
        ];

        if($this->todo->update($id, $data)) {
            return response()->json([
                'message' => $this->todo->getById($id)
            ], Response::HTTP_OK);
        }
    }

    /**
     * Delete a Todo for an user
     * 
     * @param Request $request
     * @param String $id
     * 
     * @return JsonResponse
     */
    public function delete(Request $request, $id) 
    {
        $userId = $request->user()->id;

        $isAuthorized = $this->authorizeTodoId($userId, $id);

        if($isAuthorized === false) {
            return response()->json([
                'message' => 'Invalid Todo ID: ' . $id,
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        if($this->todo->delete($id)) {
            return response()->json([
                'message' => "Todo Id Deleted: " . $id, 
            ], Response::HTTP_OK);
        }        
    }

    /**
     * Get all todo's for an arbitrary user
     * 
     * @param Integer $id
     * 
     * @return JsonResponse
     */
    public function listByUserId(int $id) 
    {
        return response()->json([
            'message' => $this->getTodoListByUser($id)
        ], Response::HTTP_OK);
    }

    /**
     * Get the list of todo's 
     * 
     * @param String $id
     * 
     * @return Mixed throwable | JsonResponse
     */
    private function getTodoListByUser($id) 
    {
        return User::findorfail($id)->todonotes()->get();
    }

    /**
     * Authorize Todo 
     * 
     * @param Integer $userId
     * @param Integer $todoId
     * 
     * @return Boolean
     */
    private function authorizeTodoId($userId, $todoId) 
    {
        $exists = TodoNote::where('id', $todoId)
                    ->where('user_id', $userId)
                    ->exists();

        return $exists;
    }
}