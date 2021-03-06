<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddTagsApiRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'photo_id' => 'required|exists:photos,id',
            'litter' => 'required_without:tags|array',
            'tags' => 'required_without:litter|array',
            'picked_up' => 'nullable|boolean'
        ];
    }
}
