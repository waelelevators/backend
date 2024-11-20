<?php

use App\Models\Task;
use Illuminate\Http\Request;
use Modules\Mobile\Transformers\TaskResource;

Route::group(['prefix' => 'crm'], function () {

    Route::get('/tasks', function () {
        $tasks =  Task::with('tags')->get();

        return TaskResource::collection($tasks);
    });


    Route::post('/tasks', function (Request $request) {

        $task = Task::create([
            'user_id' => auth('sanctum')->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date
        ]);

        $task->tags()->sync($request->tags);

        $task->users()->sync($request->users);
    });
});
