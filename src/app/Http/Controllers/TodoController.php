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
     * Get all the todo's for an arbitrary user
     * 
     * @param Integer $id
     * 
     * @return JsonResponse
     */
    public function listAllTodos(int $id) 
    {
        try {
            return response()->json([
                $this->todo->getAll(), Response::HTTP_OK
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