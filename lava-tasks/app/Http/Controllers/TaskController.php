<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\ListTasksRequest;
use App\Http\Requests\Task\CreateTaskRequest;
use App\Http\Requests\Task\ReorderTasksRequest;
use App\Http\Requests\Task\UpdateTasksRequest;
use App\Services\ProjectService;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    protected ?TaskService $taskService = null;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index()
    {
        $projects = (new ProjectService())->getAll();

        return view('tasks.index', ['projects' => $projects]);
    }

    public function list(ListTasksRequest $request): JsonResponse
    {
        $tasks = $this->taskService->list($request->get('project_id'));

        return response()->json([
            'success' => true,
            'tasks' => $tasks,
            'message' => 'Task Retreived Successfully.',
        ], 201);
    }

    public function store(CreateTaskRequest $request): JsonResponse
    {
        $this->taskService->store($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Task Created Successfully.',
        ], 201);
    }

    public function get(int $id): JsonResponse
    {
        $task = $this->taskService->getById($id);

        if ($task) {
            return response()->json([
                'success' => true,
                'task' => $task,
                'message' => 'Task Retreived Successfully',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Task not Found!',
            ], 404);
        }
    }

    public function update(UpdateTasksRequest $request, int $id): JsonResponse
    {
        $this->taskService->update($id, $request->all());

        return response()->json([
            'success' => true,
            'message' => "Task Updated Successfully",
        ], 201);
    }

    public function delete(int $id): JsonResponse
    {
        $this->taskService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Task Deleted Successfully',
        ], 201);
    }

    public function reorder(ReorderTasksRequest $request): JsonResponse
    {
        $this->taskService->reorder(
            $request->get('project_id'),
            $request->get('start'),
            $request->get('end')
        );

        return response()->json([
            'success' => true,
            'message' => 'Tasks Reordered Successfully',
        ], 201);
    }
}
