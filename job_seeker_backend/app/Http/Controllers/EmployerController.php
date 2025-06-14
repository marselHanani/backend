<?php

namespace App\Http\Controllers;

use App\Models\Employer;
use Illuminate\Http\Request;

class EmployerController extends Controller
{
    public function index()
    {
        $employers = Employer::all();
        return response()->json($employers);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'company_name' => 'required|string|max:255',
            'company_address' => 'nullable|string',
            'company_phone' => 'nullable|string',
            'company_email' => 'nullable|email',
            'company_description' => 'nullable|string',
            'company_logo' => 'nullable|string',
            'company_cover' => 'nullable|string',
            'company_social' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $employer = Employer::create($data);
        return response()->json($employer, 201);
    }

    public function show($id)
    {
        $employer = Employer::findOrFail($id);
        return response()->json($employer);
    }

    public function update(Request $request, $id)
    {
        $employer = Employer::findOrFail($id);

        $data = $request->validate([
            'company_name' => 'sometimes|required|string|max:255',
            'company_address' => 'sometimes|nullable|string',
            'company_phone' => 'sometimes|nullable|string',
            'company_email' => 'sometimes|nullable|email',
            'company_description' => 'sometimes|nullable|string',
            'company_logo' => 'sometimes|nullable|string',
            'company_cover' => 'sometimes|nullable|string',
            'company_social' => 'sometimes|nullable|string',
            'status' => 'sometimes|nullable|string',
        ]);

        $employer->update($data);
        return response()->json($employer);
    }

    public function destroy($id)
    {
        $employer = Employer::findOrFail($id);
        $employer->delete();
        return response()->json(null, 204);
    }
}
