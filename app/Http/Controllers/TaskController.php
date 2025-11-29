<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\TaskResource;
use App\Http\Requests\TaskPostRequest;


class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->user()->tasks();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $tasks = $query->paginate(10);
        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskPostRequest $request)
    {
        //
        $validated = $request->validated();
        $task = $request->user()->tasks()->create($validated);
        return new TaskResource($task);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Task $task)
    {
        //
        if ($request->user()->cannot('view', $task)) {
            abort(403);
        }
        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskPostRequest $request, Task $task)
    {
        //
        if ($request->user()->cannot('update', $task)) {
            abort(403);
        }

        $validated = $request->validated();
        $task->update($validated);
        return new TaskResource($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Task $task)
    {
        //
        if ($request->user()->cannot('delete', $task)) {
            abort(403);
        }
        $task->delete();
        return response()->json($task);
    }
}
