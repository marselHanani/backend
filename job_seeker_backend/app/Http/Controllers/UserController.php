<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $limit = request()->query('limit', 10);
        $search = request()->query('search');
        $skip = request()->query('skip', 0);
        $query = User::query();
        $total = $query->count();
        if($search){
            $query->where('first_name', 'like', '%' . $search . '%')
            ->orWhere('last_name', 'like', '%' . $search . '%');
        }
        $users = $query->skip($skip)->take($limit)->get();
        return response()->json([
            'result' => $users,
            'message' => 'Users retrieved successfully',
           'status' => 200,
          'total' => $total
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $userValidate = $request->validate([
            'first_name' => 'required|string|min:3|max:10',
            'last_name' => 'required|string|min:3|max:10',
            'username' => 'required|string|min:6',
            'email' => 'required|string|email|unique:users',
            'password' =>'required|string|min:6',
            'role_id' =>'required|integer|min:1|max:3',
        ]);
        $user = User::create($userValidate);
        return response()->json([
            'result' => $user,
            'message' => 'User created successfully',
            'status' => 201
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        if(!empty($user)){
            return response()->json([
                'result' => $user,
                'message' => 'User retrieved successfully',
                'status' => 200
            ]);
        }else{
            return response()->json([
               'message' => 'User not found',
               'status' => 404
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        if(!empty($user)){
            $user->update($request->all());
            return response()->json([
               'result' => $user,
               'message' => 'User updated successfully',
               'status' => 200
            ]);
        }else{
            return response()->json([
              'message' => 'User not found',
              'status' => 404
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if(!empty($user)){
            $user->delete();
            return response()->json([
              'message' => 'User deleted successfully',
              'status' => 200
            ]);
        }else{
            return response()->json([
             'message' => 'User not found',
             'status' => 404
            ]);
        }
    }
}
