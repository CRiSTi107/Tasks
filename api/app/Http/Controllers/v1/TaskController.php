<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Task;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Get all tasks from current user
     * @param Task $task
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Task $task, User $user)
    {
        try{
            $tasks = $task->where([
                'user_id' => $user->getPayload()['id']
            ])->get();
            return $this->returnSucces($tasks);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    /**
     * Add a new task
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(Request $request, User $user)
    {
        try {
            $rules = [
                'name' => 'required',
                'description' => 'required',
                'status' => 'required',
                'assign' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);

            if (!$validator->passes()) {
                return $this->returnBadRequest('Please fill all required fields');
            }

            $task = new Task();

            $task->name = $request->name;
            $task->description = $request->description;
            $task->status = $request->status;
            $task->user_id = $user->getPayload()['id'];
            $task->assign = $request->assign;

            $task->save();

            return $this->returnSuccess($task);

        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    /**
     * Update a task
     * @param Request $request
     * @param User $user
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $user, $id)
    {
        try {
            $task = Task::find($id);

            if($task->user_id != $user->getPayload()['id']) {
                return $this->returnError('You do not own this task.');
            }

            if ($request->has('name')) {
                $task->name = $request->name;
            }
            if ($request->has('description')) {
                $task->description = $request->description;
            }
            if ($request->has('status')) {
                $task->status = $request->status;
            }
            if ($request->has('assign')) {
                $task->assign = $request->assign;
            }

            $task->save();

            return $this->returnSuccess($task);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }
}
