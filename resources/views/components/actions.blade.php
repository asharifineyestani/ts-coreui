
<a href="{{$crud->route('show' , $id)}}" class="btn btn-success">View</a>
@can($crud->permission('update')) <a href="{{$crud->route('edit' , $id)}}" class="btn btn-primary">Edit</a> @endcan
@can($crud->permission('delete')) <button  data-remote="{{$crud->route('delete' , $id)}}" class="btn btn-danger ">Delete</button> @endcan




