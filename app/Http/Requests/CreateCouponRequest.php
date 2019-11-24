<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCouponRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'count' => 'required|integer|min:1',
            'price' => 'required|integer|min:1',
            'infinite' => 'boolean',
            'reusable' => 'boolean',
            'active_from' => 'date_format:Y-m-d\TH:i',
            'active_to' => 'date_format:Y-m-d\TH:i',
        ];
    }
}
