<?php

namespace App\Http\Requests\AgentTools;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ExecuteToolRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tool' => ['required', 'string'],
            'parameters' => ['sometimes', 'array'],
        ];
    }
}
