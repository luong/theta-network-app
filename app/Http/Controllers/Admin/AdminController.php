<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{

    public function list()
    {
        $admins = Admin::all();
        return view('admin.admin.list', [
            'admins' => $admins
        ]);
    }

    public function add()
    {
        if (request()->isMethod('post')) {
            request()->validate([
                'username' => ['bail', 'required', 'string', 'unique:admins'],
                'name' => ['bail', 'required', 'string'],
                'email' => ['bail', 'required', 'string', 'email'],
                'role' => ['bail', 'required', 'string', Rule::in(Admin::ROLES)],
                'password' => ['bail', 'required', 'confirmed', Password::min(8)]
            ]);
            $data = request()->only('username', 'name', 'email', 'role');
            $data['password'] = Hash::make(request('password'));
            Admin::create($data);
            return back()->with('message', 'Added successfully.');
        }
        return view('admin.admin.add', [
            'roles' => Admin::ROLES
        ]);
    }

    public function edit($id)
    {
        $admin = Admin::find($id);
        if (request()->isMethod('post')) {
            request()->validate([
                'username' => ['bail', 'required', 'string', Rule::unique('admins')->ignore($admin->username, 'username')],
                'name' => ['bail', 'required', 'string'],
                'email' => ['bail', 'required', 'string', 'email'],
                'role' => ['bail', 'required', 'string', Rule::in(Admin::ROLES)]
            ]);
            $data = request()->only('username', 'name', 'email', 'role');
            if (request()->filled('password')) {
                request()->validate([
                    'password' => ['bail', 'required', 'confirmed', Password::min(8)]
                ]);
                $data['password'] = Hash::make(request('password'));
            }
            $admin->update($data);
            return back()->with('message', 'Edited successfully.');
        }
        return view('admin.admin.edit', [
            'admin' => $admin,
            'roles' => Admin::ROLES
        ]);
    }

    public function delete($id)
    {
        Admin::destroy($id);
        return back();
    }

}
