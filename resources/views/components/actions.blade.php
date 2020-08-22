
<a href="/{{$entities}}/{{$id}}" class="btn btn-success">View</a>
@can($entities.'-update') <a href="/{{$entities}}/{{$id}}/edit" class="btn btn-primary">Edit</a> @endcan
@can($entities.'-delete') <button  data-remote="/{{$entities}}/{{$id}}" class="btn btn-danger ">Delete</button> @endcan



