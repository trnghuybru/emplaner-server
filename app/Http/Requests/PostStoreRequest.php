<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostStoreRequest extends FormRequest
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
    public function rules()
    {
        $rules = [
            'name' => 'string|max:255',
            'job' => 'string|max:255',
            'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        if ($this->isMethod('POST')) {
            $rules['name'] .= '|required';
            $rules['job'] .= '|required';
            $rules['avatar'] .= '|required';
        }

        return $rules;
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        $messages = [
            'name.required' => 'Name is required!',
            'job.required' => 'Job is required!',
            'avatar.required' => 'Avatar is required!',
        ];

        if (!$this->isMethod('POST')) {
            unset($messages['avatar.required']);
        }

        return $messages;
    }
}
