<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(['tecnico', 'desarrollo', 'acceso', 'facturacion', 'otro'])],
            'priority' => ['required', Rule::in(['baja', 'media', 'alta'])],
            'message' => ['required', 'string', 'min:10'],
        ];
    }
}
