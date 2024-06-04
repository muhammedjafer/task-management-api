<?php

namespace App\Http\Requests\User;

use App\Enums\RoleEnum;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
        $roleValues = RoleEnum::values();

        return [
            'name' => 'required|string',
            'password' => 'required|min:8',
            'email' => 'required|unique:users,email|string|email',
            'role' => 'required|int|in:'.implode(',', $roleValues)
        ];
    }
}
