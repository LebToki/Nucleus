<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDelegationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $delegation = $this->route('delegation');
        $user = auth()->user();
        return $user && ($delegation->assignee_id === $user->id || $user->hasRole('owner'));
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['sometimes', 'in:high,medium,normal,low'],
            'due_date' => ['nullable', 'date'],
            'flag_reason' => ['nullable', 'string'],
        ];
    }
}
