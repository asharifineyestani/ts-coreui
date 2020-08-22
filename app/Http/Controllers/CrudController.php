<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CrudController extends Controller
{


    public $columns = [];
    public $route;
    public $model;
    public $entities;
    public $entity;


    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
        $this->setupListOperation();
        $this->setup();

    }


    public function setup()
    {
        $this->setModel("App\Models\Status");
        $this->setRoute("admin/tag");
        $this->setEntityNameStrings('user', 'users');

    }

    public function setupListOperation()
    {
        $this->setColumns(['id', 'name', 'email', 'created_at', 'updated_at', 'action']);
    }


    public function index()
    {
        $datable_columns = "[";
        foreach ($this->columns as $field):
            $datable_columns .= "{data: '" . $field['data'] . "', name: '" . $field['data'] . "', orderable: " . ($field['orderable'] ? 'true' : 'false') . ", searchable: " . ($field['searchable'] ? 'true' : 'false') . "},";
        endforeach;
        $datable_columns .= "]";

        $you = auth()->user();
        $users = $this->model::all();
        return view('dashboard.crud.datatable',
            [
                'users' => $users,
                'you' => $you,
                'entities' => $this->entities,
                'columns' => $this->columns,
                'datable_columns' => $datable_columns,
            ]
        );
    }

    public function show($id)
    {
        $user = User::find($id);
        return view('dashboard.admin.userShow', compact('user'));
    }

    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();
        return view('dashboard.admin.userEditForm', compact('user', 'roles', 'userRole'));
    }

    public function update(UserRequest $request, $id)
    {

        $input = $request->all();
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = $request->except(['password']);

        }
        $user = User::find($id);
        $user->update($input);
        \DB::table('model_has_roles')->where('model_id', $id)->delete();
        $user->assignRole($request->input('roles'));
        return back()->with('success', 'User updated successfully');
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
        }
        return true;
    }


    public function dataTable()
    {
        $data = $this->model::select('*');
        return \Yajra\DataTables\DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return view('components.actions', ['id' => $row->id, 'entities' => $this->entities]);
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function setColumns($columns)
    {
        $this->columns = [];

        foreach ($columns as $column) {
            array_push($this->columns,
                [
                    'data' => $column,
                    'name' => $column,
                    'orderable' => 1,
                    'searchable' => 1,
                ]
            );
        }

        return $this;
    }


    public function setColumn($data, $title = null, $orderable = null, $searchable = null)
    {

        array_push($this->columns,
            [
                'data' => $data,
                'name' => $title ?? ucfirst($data),
                'orderable' => $orderable ?? 1,
                'searchable' => $searchable ?? 1,
            ]
        );

        return $this;
    }


    public function setRoute(string $route)
    {
        $this->route = $route;
    }

    public function setModel(string $model)
    {
        $this->model = $model;
    }

    public function setEntityNameStrings(string $entity, string $entities)
    {
        $this->entities = $entities;
        $this->entity = $entity;
    }
}
