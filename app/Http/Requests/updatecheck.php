<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class updatecheck extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'=>'max:170|alpha',
            'email'=>'email|max:100',
            'age'=>'min:1|numeric',
            'password'=>'min:7|',
            'confirm password'=>'|same:password',
        ];
    }
}
