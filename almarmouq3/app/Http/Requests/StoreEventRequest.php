<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'start_time' => ['required', 'date'],
            'end_time' => ['nullable', 'date', 'after:start_time'],
            'location' => ['nullable', 'string', 'max:255'],
            'location_ar' => ['nullable', 'string', 'max:255'],
            'host_name' => ['nullable', 'string', 'max:255'],
            'host_name_ar' => ['nullable', 'string', 'max:255'],
            'guest_of_honor' => ['nullable', 'string', 'max:255'],
            'guest_of_honor_ar' => ['nullable', 'string', 'max:255'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'protocol_notes' => ['nullable', 'string'],
            'protocol_notes_ar' => ['nullable', 'string'],
            'dietary_restrictions' => ['nullable', 'string'],
            'dietary_restrictions_ar' => ['nullable', 'string'],
            'priority' => ['sometimes', 'in:high,medium,normal,low'],
            'status' => ['sometimes', 'in:scheduled,active,completed,cancelled'],
            'tags_input' => ['nullable', 'string'],
            'required_materials_input' => ['nullable', 'string'],
            'checklist_items' => ['nullable', 'string'],
        ];
    }
}
