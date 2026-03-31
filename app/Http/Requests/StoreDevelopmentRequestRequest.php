<?php

namespace App\Http\Requests;

use App\Models\DevelopmentRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDevelopmentRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:255'],
            'priority' => ['required', Rule::in(array_keys(DevelopmentRequest::priorityOptions()))],
            'message' => ['required', 'string', 'min:10'],
        ];
    }
}
