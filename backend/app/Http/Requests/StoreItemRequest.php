<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        // ignore unique rule if item already exists in the deleted list because it can be restored
        $trashed_item = auth()->user()
            ->items()
            ->withTrashed()
            ->where('deleted_at', '!=', null)
            ->where('name', $this->input('name'))
            ->first();

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('items')->ignore(optional($trashed_item)->id ?? 0), // ignore all the non deleted items
            ]
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name field must be a string.',
            'name.max' => 'The name field must be no longer than 255 characters.',
            'name.unique' => 'An item with that name already exists.',
        ];
    }
}
