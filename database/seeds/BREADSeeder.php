<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class BREADSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('form')->insert([
            'name' => 'Example',
            'table_name' => 'example',
            'read' => 1,
            'edit' => 1,
            'add' => 1,
            'delete' => 1,
            'pagination' => 5
        ]);
        $formId = DB::getPdo()->lastInsertId();
        DB::table('form_field')->insert([
            'name' => 'Title',
            'type' => 'text',
            'browse' => 1,
            'read' => 1,
            'edit' => 1,
            'add' => 1,
            'form_id' => $formId,
            'column_name' => 'name'
        ]);
        DB::table('form_field')->insert([
            'name' => 'Description',
            'type' => 'text_area',
            'browse' => 1,
            'read' => 1,
            'edit' => 1,
            'add' => 1,
            'form_id' => $formId,
            'column_name' => 'description'
        ]);
        DB::table('form_field')->insert([
            'name' => 'Status',
            'type' => 'relation_select',
            'browse' => 1,
            'read' => 1,
            'edit' => 1,
            'add' => 1,
            'form_id' => $formId,
            'column_name' => 'status_id',
            'relation_table' => 'status',
            'relation_column' => 'name'
        ]);
        $role = Role::where('name', '=', 'guest')->first();
        Permission::create(['name' => 'browse bread ' . $formId]);
        Permission::create(['name' => 'read bread ' . $formId]);
        Permission::create(['name' => 'edit bread ' . $formId]);
        Permission::create(['name' => 'add bread ' . $formId]);
        Permission::create(['name' => 'delete bread ' . $formId]);
        Permission::create(['name' => 'users-read']);
        Permission::create(['name' => 'users-insert']);
        Permission::create(['name' => 'users-update']);
        Permission::create(['name' => 'users-delete']);
        Permission::create(['name' => 'notes-read']);
        Permission::create(['name' => 'notes-insert']);
        Permission::create(['name' => 'notes-update']);
        Permission::create(['name' => 'notes-delete']);
        $role->givePermissionTo('browse bread ' . $formId);
        $role->givePermissionTo('read bread ' . $formId);
        $role->givePermissionTo('edit bread ' . $formId);
        $role->givePermissionTo('add bread ' . $formId);
        $role->givePermissionTo('delete bread ' . $formId);


        $admin = Role::where('name', '=', 'admin')->first();
        $admin->givePermissionTo('users-read');
        $admin->givePermissionTo('users-insert');
        $admin->givePermissionTo('users-update');
        $admin->givePermissionTo('users-delete');
        $admin->givePermissionTo('notes-read');
        $admin->givePermissionTo('notes-insert');
        $admin->givePermissionTo('notes-update');
        $admin->givePermissionTo('notes-delete');
    }
}
