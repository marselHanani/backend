<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class JobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:30|min:3',
            'description' => 'required|string',
            'salary_minimum' => 'required|numeric',
            'salary_maximum' =>'required|numeric',
            'location' => 'required|string|max:50',
            'experience' =>'required|string|max:50',
            'requirements' =>'required|string',
            'responsibilities' =>'required|string',
            'education' =>'nullable|string',
            'vacancies' =>'required|integer|min:1|max:10',
            'expiration' =>'required|date',
            'time_type' =>'required|string',
            'job_level' =>'required|string',
            'job_type' =>'required|string',
            'job_role' =>'required|string',
            'city' =>'required|string',
           'street' =>'nullable|string',
            'tags' =>'nullable|string',
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
