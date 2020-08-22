<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Menus;
use App\Models\Notes;
use Illuminate\Http\Request;
use App\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UsersController extends CrudController
{

    public function setup()
    {
        $this->setModel(User::class);
        $this->setEntityNameStrings('note', 'notes');

    }


    public function setupListOperation()
    {
        $this->setColumn('id');
        $this->setColumn('name');
        $this->setColumn('email');
        $this->setColumn('action');
//        $this->setColumn('action');
//        $this->setColumn('created_at');
    }


}
