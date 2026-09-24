<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ChoreRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|integer|max:100|min:10',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'お手伝い名を入力してください',
            'name.string' => 'お手伝い名は文字列で入力してください',
            'name.max' => 'お手伝い名は255文字以内で入力してください',
            'price.required' => 'お小遣いの金額を設定してください',
            'price.integer' => 'お小遣いの金額は数値で入力してください',
            'price.max' => 'お小遣いの金額は100円以下で設定してください',
            'price.min' => 'お小遣いの金額は10円以上で設定してください',
        ];
    }
}
