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
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\User;

class TodoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TodoNote $todo)
    {
        $this->todo = new CRUDRepository($todo);
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
        $userId = $request->user()->id;

        try {
            return response()->json([
                "message" => $this->getTodoListByUser($userId)
            ], Response::HTTP_OK);
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

        $validator = Validator::make($input, $rules, $messages);

        // Check if the parameters passed are invalid
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->messages()->all(),
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        try {
            return response()->json([
                'message' => $this->todo->create($input)
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => "No Query Results Found for: " . $id],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a Todo for an user
     * 
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function update(Request $request, $id) 
    {
        $userId = $request->user()->id;

        $exists = TodoNote::where('id', $id)
                ->where('user_id', $userId)
                ->exists();

        if(!$exists) {
            return response()->json([
                'message' => 'Invalid Todo ID: ' . $id,
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        $input = [
            'completed' => $request->completed
        ];

        $rules = [
            'completed' => 'required|boolean'
        ];

        $messages = [
            'required' => 'The :attribute is required.'
        ];

        $validator = Validator::make($input, $rules, $messages);

        // Check if the parameters passed are invalid
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->messages()->all(),
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        try {
            $data = [
                'completed_at' => $request->completed == 1 ? Carbon::now() : null
            ];
            if($this->todo->update($id, $data)) {
                return response()->json([
                    'message' => $this->todo->getById($id)
                ], Response::HTTP_OK);
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => "No Query Results Found for: " . $id],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a Todo for an user
     * 
     * @param Request $request
     * 
     * @return JsonResponses
     */
    public function delete(Request $request, $id) 
    {
        try {
            $userId = $request->user()->id;
            $exists = TodoNote::where('id', $id)
                ->where('user_id', $userId)
                ->exists();

            if(!$exists) {
                return response()->json([
                    'message' => 'Invalid Todo ID: ' . $id,
                ], Response::HTTP_NOT_ACCEPTABLE);
            }
    
            return response()->json([
                'message' => $this->todo->delete($id), 
            ],Response::HTTP_NO_CONTENT);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => "No Query Results Found for: " . $id],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR);
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
        try {
            return response()->json([
                'message' => $this->getTodoListByUser($id)
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => "No Query Results Found for: " . $id],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function getTodoListByUser($id) {
        return User::findorfail($id)->todonotes()->get();
    }
}