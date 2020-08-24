<?php

namespace App\Http\Controllers;

use App\Classes\Crud;
use App\Http\Requests\UserRequest;
use App\Models\Menus;
use App\Models\Notes;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UsersController extends CrudController
{

    public function config()
    {
        $this->crud->setModel(User::class);
        $this->crud->setEntity('users');
    }


    public function setupIndex()
    {
        $this->crud->setColumn('id');
        $this->crud->setColumn('name');
        $this->crud->setColumn('email');
        $this->crud->setColumn('action');
    }

    public function setupCreate()
    {

        $this->crud->setField(
            [
                'type' => 'text',
                'label' => 'نام و نام خانوادگی',
                'name' => 'name',
                'validation' => 'required|string'
            ]
        );


        $this->crud->setField(
            [
                'type' => 'email',
                'label' => 'ایمیل',
                'name' => 'email',
                'validation' => 'required|email|unique:users,email'
            ]
        );


        $this->crud->setField(
            [
                'type' => 'password',
                'label' => 'پسورد',
                'name' => 'password',
            ]
        );


        $this->crud->setField(
            [
                'type' => 'select2_multiple',
                'label' => 'نقش ها',
                'name' => 'roles',
                'model' => Role::class,
                'attribute' => 'name',
            ]
        );


    }

    public function setupEdit()
    {
        $this->crud->setField(
            [
                'type' => 'text',
                'label' => 'نام و نام خانوادگی',
                'name' => 'name',
                'validation' => 'required|string',
            ]
        );


        $this->crud->setField(
            [
                'type' => 'email',
                'label' => 'ایمیل',
                'name' => 'email',
            ]
        );


        $this->crud->setField(
            [
                'type' => 'password',
                'label' => 'پسورد',
                'name' => 'password',
            ]
        );


        $this->crud->setField(
            [
                'type' => 'select2_multiple',
                'label' => 'نقش ها',
                'name' => 'roles',
                'model' => Role::class,
                'attribute' => 'name',

            ]
        );

    }


    public function update(Request $request, $id)
    {

        $this->setupEdit();

        $this->validate($request, array_merge($this->crud->getValidations(), ['email' => 'required|email|unique:users,email,' . $id]));

        $input = $request->only($this->crud->getFields('name'));

        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password'));

        }


        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id', $id)->delete();
        $user->assignRole($request->input('roles'));
        return redirect()->route('users.index')
            ->with('success', 'User updated successfully');
    }


    public function store(Request $request)
    {

        $validator = [];

        return $this->crud;
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $user->assignRole($request->input('roles'));
        return redirect()->route('users.index')
            ->with('success', 'User created successfully');
    }




}
