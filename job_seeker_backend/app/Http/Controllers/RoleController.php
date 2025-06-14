<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all();
        if($roles != null){
            return response()->json([
                'status' => 200,
                'message' => 'Roles retrieved successfully',
                'roles' => $roles
            ]);
        }
        else{
            return response()->json([
                'status' => 404,
                'message' => 'Roles not found'
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $role = $request->validate([
            'name'=> 'required|string|unique:roles,name,',
            ]);
            if($role){
                $role = Role::create($role);
                return response()->json([
                    'status' => 200,
                    'message' => 'Role created successfully',
                    'role' => $role
                ]);
            }
            else{
                return response()->json([
                    'status' => 404,
                    'message' => 'Role not created'
                ]);
            }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::find($id);
        if($role != null){
            return response()->json([
               'status' => 200,
               'message' => 'Role retrieved successfully',
                'role' => $role
            ]);
        }
        else{
            return response()->json([
               'status' => 404,
               'message' => 'Role not found'
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::find($id);
        if($role != null){
            $role = Role::update($request->all());
            return response()->json([
               'status' => 200,
               'message' => 'Role updated successfully',
                'role' => $role
            ]);
        }
        else{
            return response()->json([
               'status' => 404,
               'message' => 'Role not found'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::find($id);
        if($role!= null){
            $role = Role::delete();
            return response()->json([
              'status' => 200,
              'message' => 'Role deleted successfully',
                'role' => $role
            ]);
        }
        else{
            return response()->json([
                'status'=> 404,
                'message' => 'Role not found'
            ]);
        }
    }
}
