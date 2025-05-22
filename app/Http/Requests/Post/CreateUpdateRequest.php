<?php

namespace App\Http\Requests\Post;

use App\Http\Requests\ReqValidator;

class CreateUpdateRequest extends ReqValidator
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'title' => 'string|max:60',
            'content' => 'string',
            'publish_date' => 'date_format:Y-m-d',
            'is_draft' => 'boolean'
        ];
        
        if($this->getMethod() == 'POST'){
            $rules = $this->addRequired($rules, ['is_draft']);
        }

        return $rules;
    }
}
