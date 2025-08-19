<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->hasAnyRole(['Admin','SuperAdmin']);
    }

    public function rules()
    {
        return [
            'name' => ['required','string','max:255','regex:/^[A-Za-z0-9\s\-\_\.]+$/u'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:8','confirmed'],
            'role' => ['required','string','exists:roles,name'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'name' => strip_tags($this->name),
            'email' => strip_tags($this->email),
        ]);
    }

    public function messages()
    {
        return [
            'name.regex' => 'Name may contain only letters, numbers, spaces, dash, underscore and dot.',
        ];
    }
}
