<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use \App\Task;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::all();
        return view('home', compact('tasks'));
    }

    public function addTask(Request $request) {
        $data = $request->all();
        $data['user_id'] = auth()->id();
        Task::create($data);
        //print_r($data);
        return Redirect::back();
    }

    public function editTask(Request $request) {
        $task = Task::findOrFail($request->id);
        $task->update($request->all());

        return Redirect::back();
    }

    public function deleteTask(Request $request) {
        //dd($request->all());
        $task = Task::findOrFail($request->id);
        $task->delete();
        return Redirect::back();
    }
}
