<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = auth('api')->user();
        return [
            'name' => 'required|string|max:70',
            'email' => 'required|string|email|max:100|unique:users,email,'.$user->id,
            'password' => 'sometimes|min:8'
        ];
    }
}
