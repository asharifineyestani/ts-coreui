<?php

namespace App\Http\Controllers;

use App\Classes\Crud;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
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


    public function update(Request $request, $id)
    {
        $this->crud->setRow($id);
        $this->setupEdit();


        $this->validate($request, array_merge($this->crud->getValidations()));

        $input = $request->only($this->crud->getFields('name'));

        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password'));

        }

        $this->crud->row->update($input);
        DB::table('model_has_roles')->where('model_id', $id)->delete();
        $this->crud->row->assignRole($request->input('roles'));


        $input = $request->all();
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = $request->except(['password']);
        }


        $media = $this->crud->row->getMedia('*')->pluck('file_name')->toArray();


        foreach ($request->input('mediable', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $this->crud->row->addMedia(storage_path('tmp/' . $file))->toMediaCollection();
            }
        }


        return redirect()->route('users.index')
            ->with('success', 'User updated successfully');
    }


    public function store(Request $request)
    {

        $this->setupCreate();

        $this->validate($request, $this->crud->getValidations());

        $fields = $request->only($this->crud->getFields('name'));

        $new = $this->crud->model::create($fields);

        foreach ($request->input('mediable', []) as $file) {

            $new->addMedia(storage_path('tmp/' . $file))->toMediaCollection();
        }

        return redirect($this->crud->route('index'));
    }


    public function destroy($id)
    {
        $row = $this->crud->model::find($id);

        foreach ($row->getMedia('*') as $media)
            $media->delete();

        $row->delete();
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


    public function storeMedia(Request $request)
    {
        $path = storage_path('tmp');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file = $request->file('file');

        $name = uniqid() . '_' . trim($file->getClientOriginalName());

        $file->move($path, $name);

        return response()->json([
            'name'          => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);

    }


    public function getMedia($id)
    {

        $row = $this->crud->model::find($id);

        $medias = $row->getMedia('*');


        $result = [];

        foreach ($medias as $media)
            $result[] = [
                "name" => $media->name,
                "size" => $media->size,
                "url" => $media->getUrl(),
            ];

        return $result;


    }

}
