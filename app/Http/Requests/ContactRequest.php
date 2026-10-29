<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150'],
            'subject' => ['required', 'string', 'min:3', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'website' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please tell me your name.',
            'email.required' => 'I need an email address to reply to.',
            'email.email' => 'That email address doesn\'t look quite right.',
            'subject.required' => 'Please add a short subject.',
            'message.required' => 'Please write a message.',
            'message.min' => 'Could you add a little more detail? At least :min characters.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(collect($this->only(['name', 'email', 'subject', 'message']))
            ->map(fn ($value) => is_string($value) ? trim($value) : $value)
            ->all());
    }

    public function isSpam(): bool
    {
        return filled($this->input('website'));
    }
}
