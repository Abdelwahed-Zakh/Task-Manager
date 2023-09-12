<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
 * @OA\Get(
 *     path="/api/tasks",
 *     tags={"Tasks"},
 *     summary="Get all tasks",
 *     operationId="index",
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Response(
 *         response=200,
 *         description="successful operation",
 *         @OA\JsonContent(
 *             type="string"
 *         ),
 *         @OA\MediaType(
 *             mediaType="application/xml",
 *             @OA\Schema(
 *                 type="string"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 */
    public function index()
    {
        $data = Task::paginate(9);
        return TaskResource::collection($data);
    }


     /**
 * @OA\Post(
 *     path="/api/tasks",
 *     tags={"Tasks"},
 *     summary="Create a new task",
 *     operationId="store",
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="title", type="string", example="Task Title"),
 *             @OA\Property(property="description", type="string", example="Task Description"),
 *             @OA\Property(property="due_date", type="string", format="date", example="2023-12-31"),
 *             @OA\Property(property="status", type="string", enum={"pending", "completed"}, example="pending")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Task created successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="id", type="integer", format="int64", example=1),
 *             @OA\Property(property="title", type="string", example="Task Title"),
 *             @OA\Property(property="description", type="string", example="Task Description"),
 *             @OA\Property(property="due_date", type="string", format="date", example="2023-12-31"),
 *             @OA\Property(property="status", type="string", enum={"pending", "completed"}, example="pending"),
 *             @OA\Property(property="user_id", type="integer", format="int64", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Unprocessable Entity (Validation Error)"
 *     )
 * )
 */
    public function store(StoreTaskRequest $request)
    {
            $task = new Task();
            $task->title = $request->title;
            $task->description = $request->description;
            $task->due_date = $request->due_date;
            $task->status = $request->status;
            $task->user_id = auth()->id();
            $task->save();

            return new TaskResource($task);
    }


     /**
 * @OA\Get(
 *     path="/api/tasks/{id}",
 *     tags={"Tasks"},
 *     summary="Retrieve a specific task by ID",
 *     operationId="show",
 *      security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the task to retrieve",
 *         @OA\Schema(type="integer", format="int64", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Task retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="id", type="integer", format="int64", example=1),
 *             @OA\Property(property="title", type="string", example="Task Title"),
 *             @OA\Property(property="description", type="string", example="Task Description"),
 *             @OA\Property(property="due_date", type="string", format="date", example="2023-12-31"),
 *             @OA\Property(property="status", type="string", enum={"pending", "completed"}, example="pending"),
 *             @OA\Property(property="user_id", type="integer", format="int64", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Task not found"
 *     )
 * )
 */
    public function show(Task $task)
    {
        return new TaskResource($task);
    }

   /**
 * @OA\Put(
 *     path="/api/tasks/{id}",
 *     tags={"Tasks"},
 *     summary="Update a specific task by ID",
 *     operationId="update",
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the task to update",
 *         @OA\Schema(type="integer", format="int64", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="title", type="string", maxLength=255, example="Updated Task Title"),
 *             @OA\Property(property="due_date", type="string", format="date", example="2023-12-31"),
 *             @OA\Property(property="description", type="string", example="Updated Task Description"),
 *             @OA\Property(property="status", type="string", enum={"pending", "in progress", "completed"}, example="completed")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Task updated successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="id", type="integer", format="int64", example=1),
 *             @OA\Property(property="title", type="string", example="Updated Task Title"),
 *             @OA\Property(property="description", type="string", example="Updated Task Description"),
 *             @OA\Property(property="due_date", type="string", format="date", example="2023-12-31"),
 *             @OA\Property(property="status", type="string", enum={"pending", "in progress", "completed"}, example="completed"),
 *             @OA\Property(property="user_id", type="integer", format="int64", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Task not found"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden. You don't have permission to update this task."
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Unprocessable Entity (Validation Error)"
 *     )
 * )
 */
    public function update(Request $request, Task $task)
    {

        $this->authorize('manage-tasks',$task);

        $task->update($request->validate([
            'title' => 'sometimes|max:255',
            'due_date' => 'sometimes|date',
            'description' => 'nullable',
            'status' => 'sometimes|in:pending,in progress,completed'
        ]));

        return new TaskResource($task);
    }

   /**
 * @OA\Delete(
 *     path="/api/tasks/{id}",
 *     tags={"Tasks"},
 *     summary="Delete a specific task by ID",
 *     operationId="destroy",
 *     security={
 *         {"bearerAuth": {}}
 *     },
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the task to delete",
 *         @OA\Schema(type="integer", format="int64", example=1)
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Task deleted successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Task not found"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden. You don't have permission to delete this task."
 *     )
 * )
 */
    public function destroy(Task $task)
    {
        $this->authorize('manage-tasks',$task);
        $task->delete();
        return response(null,203);
    }
}
