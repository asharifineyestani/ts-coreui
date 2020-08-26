<?php

namespace App\Http\Controllers;

use App\Classes\Crud;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;


class CrudController extends Controller
{
    public $crud;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
        $this->crud = new Crud();

        $this->config();


    }

    public function index()
    {
        $this->setupIndex();
        return view('dashboard.afra.datatable', ['crud' => $this->crud]);
    }

    public function show($id)
    {
        $user = User::find($id);
        return view('dashboard.admin.userShow', compact('user'));
    }


    public function create()
    {
        $this->crud->resetFields();
        $this->setupCreate();

        return view('dashboard.afra.create',
            [
                'crud' => $this->crud
            ]
        );
    }

    public function edit($id)
    {

        $row = $this->crud->model::where('id', $id)->first();

        $this->crud->resetFields();
        $this->setupEdit();
        $this->crud->setDefaults($row);


        return view('dashboard.afra.edit',
            [
                'row' => $row,
                'crud' => $this->crud
            ]
        );
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


    public function store(Request $request)
    {
        $this->setupCreate();

        $this->validate($request, $this->crud->getValidations());

        $fields = $request->only($this->crud->getFields('name'));

        $new = $this->crud->model::create($fields);

        foreach ($request->input('mediable', []) as $file) {

            $new->addMedia(storage_path('tmp/' . $file))->toMediaCollection('document');
        }

        return redirect($this->crud->route('index'));
    }


    public function destroy($id)
    {

        $user = $this->crud->model::find($id);


        foreach ($user->getMedia() as $media) {
            $media->delete();
        }

        $user->delete();

        return true;
    }

    public function dataTable()
    {
        $data = $this->crud->model::select('*');
        return \Yajra\DataTables\DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return view('components.actions', ['id' => $row->id, 'crud' => $this->crud]);
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function config()
    {

    }

    public function setupIndex()
    {

    }

    public function setupCreate()
    {

    }

    public function setupEdit()
    {

    }

}
