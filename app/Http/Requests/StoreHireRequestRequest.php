<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHireRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => ['required', 'string', 'max:255'],
            'projectTitle' => ['required', 'string', 'max:255'],
            'needs' => ['required', 'string', 'min:40'],
            'outcome' => ['required', 'string', 'max:255'],
            'timeline' => ['required', 'string', 'max:255'],
            'budget' => ['required', 'string', 'max:255'],
            'team' => ['required', 'string', 'max:255'],
            'context' => ['nullable', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'source' => ['nullable', 'string', 'max:255'],
            'attachment' => ['nullable', 'file', 'max:10240'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:10240'],
            'files' => ['nullable', 'array'],
            'files.*' => ['file', 'max:10240'],
        ];
    }
}
