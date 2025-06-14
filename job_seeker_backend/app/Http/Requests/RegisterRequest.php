<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|min:3|max:10',
            'last_name' => 'required|string|min:4|max:10',
            'username' => 'required|string|min:3|max:10|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'role_id' => 'required|integer|exists:roles,id',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = [];
        foreach ($validator->errors()->messages() as $fieldErrors) {
            foreach ($fieldErrors as $error) {
                $errors[] = $error;
            }
        }
        throw new HttpResponseException(
            response()->json([
                'message' =>'Validation failed',
                'errors' => $errors
            ], 422)
        );
    }
}
