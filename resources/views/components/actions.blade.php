<a href="/{{$table}}/{{$id}}" class="btn btn-success">View</a>
@can($table.'-update') <a href="/{{$table}}/{{$id}}/edit" class="btn btn-primary">Edit</a> @endcan
@can($table.'-delete') <button  data-remote="/{{$table}}/{{$id}}" class="edit btn btn-danger btn-sm">Delete</button> @endcan



