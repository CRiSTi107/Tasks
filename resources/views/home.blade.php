@extends('layouts.base')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('tasks-table')

  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">All Tasks</h3>
    </div>
    <div class="box-body">
      <table class="table table-responsive">
        <thead>
          <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Status</th>
            <th>Assign</th>
            <th>Modify</th>
          </tr>
        </thead>
        <tbody>
          @foreach($tasks as $task)
            <tr>
              <th>{{$task->name}}</th>
              <th>{{$task->description}}</th>
              <th>{{$task->status}}</th>
              <th>{{$task->assign}}</th>
              <th>
                <button class="btn btn-info"   type="button" name="edit"   role="dialog" data-toggle="modal" data-target="#editTask"   data-id="{{$task->id}}" data-name="{{$task->name}}" data-description="{{$task->description}}" data-status="{{$task->status}}" data-assign="{{$task->assign}}">Edit</button>
                <button class="btn btn-danger" type="button" name="delete" role="dialog" data-toggle="modal" data-target="#deleteTask" data-id="{{$task->id}}" data-name="{{$task->name}}">Delete</button>
              </th>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>


@endsection


@section('tasks-menu')

<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" role="dialog" data-toggle="modal" data-target="#addTask">
  Add Task
</button>

<!-- Add Modal -->
<div class="modal fade" id="addTask" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">New Task</h4>
      </div>
      <form class="" action="{{url('add-task')}}" method="post">
        <div class="modal-body">
          {{csrf_field()}}
          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" name="name" id="name">
          </div>

          <div class="form-group">
            <label for="description">Description</label>
            <textarea type="text" class="form-control" name="description" id="description" style="height: 80px;"></textarea>
          </div>

          <div class="form-group">
            <label for="status">Status</label>
            <input type="number" class="form-control" name="status" id="status">
          </div>

          <div class="form-group">
            <label for="assign">Assign</label>
            <input type="number" class="form-control" name="assign" id="assign">
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary btn-success">Add</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Edit Modal -->
<div class="modal fade" id="editTask" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Task</h4>
      </div>
      <form class="" action="{{url('edit-task')}}" method="post">
        <div class="modal-body">
          <{{method_field('PATCH')}}>
          {{csrf_field()}}
          <input type="hidden" name="id" id="id" value="">
          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" name="name" id="name">
          </div>

          <div class="form-group">
            <label for="description">Description</label>
            <textarea type="text" class="form-control" name="description" id="description" style="height: 80px;"></textarea>
          </div>

          <div class="form-group">
            <label for="status">Status</label>
            <input type="number" class="form-control" name="status" id="status">
          </div>

          <div class="form-group">
            <label for="assign">Assign</label>
            <input type="number" class="form-control" name="assign" id="assign">
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal modal-danger fade" id="deleteTask" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-left" id="myModalLabel">Delete Task</h4>
      </div>
      <form class="" action="{{url('delete-task')}}" method="post">
        <div class="modal-body">
          <!--{{method_field('delete')}}-->
          {{csrf_field()}}
          <input type="hidden" name="id" id="id" value="">

          <div class="form-group">
            <label>Are you sure you want to delete "<label id="taskname">{task}</label>"?</label>
            <small>This action cannot be undone.</small>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
          <button type="submit" class="btn btn-primary btn-danger">Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
