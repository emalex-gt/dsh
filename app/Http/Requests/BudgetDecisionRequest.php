<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BudgetDecisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'accepted_name' => ['required', 'string', 'max:255'],
            'client_notes' => ['nullable', 'string', 'max:5000'],
            'accept_terms' => ['accepted'],
        ];
    }
}
