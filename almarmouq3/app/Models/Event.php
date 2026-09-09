<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    protected $fillable = [
        'user_id',
        'customer_id',
        'type',
        'title',
        'title_ar',
        'start_time',
        'end_time',
        'location',
        'location_ar',
        'host_name',
        'host_name_ar',
        'guest_of_honor',
        'guest_of_honor_ar',
        'protocol_notes',
        'protocol_notes_ar',
        'dietary_restrictions',
        'dietary_restrictions_ar',
        'required_materials',
        'checklist_items',
        'priority',
        'status',
        'prep_done',
        'tags',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'required_materials' => 'array',
            'checklist_items' => 'array',
            'prep_done' => 'boolean',
            'tags' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function displayName(): string
    {
        return app()->getLocale() === 'ar' && $this->title_ar
            ? $this->title_ar
            : $this->title;
    }

    public function displayLocation(): ?string
    {
        if (app()->getLocale() === 'ar' && $this->location_ar) {
            return $this->location_ar;
        }
        return $this->location;
    }

    public function displayGuest(): ?string
    {
        if (app()->getLocale() === 'ar' && $this->guest_of_honor_ar) {
            return $this->guest_of_honor_ar;
        }
        return $this->guest_of_honor;
    }

    public function displayProtocolNotes(): ?string
    {
        if (app()->getLocale() === 'ar' && $this->protocol_notes_ar) {
            return $this->protocol_notes_ar;
        }
        return $this->protocol_notes;
    }

    public function displayDietary(): ?string
    {
        if (app()->getLocale() === 'ar' && $this->dietary_restrictions_ar) {
            return $this->dietary_restrictions_ar;
        }
        return $this->dietary_restrictions;
    }

    public function displayHost(): ?string
    {
        if (app()->getLocale() === 'ar' && $this->host_name_ar) {
            return $this->host_name_ar;
        }
        return $this->host_name;
    }

    public function priorityLabel(): string
    {
        return match ($this->priority) {
            'high' => __('entities.agenda.priority_high'),
            'medium' => __('entities.agenda.priority_medium'),
            'low' => __('entities.agenda.priority_low'),
            default => __('entities.agenda.priority_normal'),
        };
    }

    public function priorityColor(): string
    {
        return match ($this->priority) {
            'high' => 'danger',
            'medium' => 'warning',
            'low' => 'success',
            default => 'neutral',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'scheduled' => __('entities.agenda.status_scheduled'),
            'confirmed' => __('entities.agenda.status_confirmed'),
            'in_progress' => __('entities.agenda.status_in_progress'),
            'completed' => __('entities.agenda.status_completed'),
            'cancelled' => __('entities.agenda.status_cancelled'),
            default => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'scheduled' => 'neutral',
            'confirmed' => 'primary',
            'in_progress' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'neutral',
        };
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'meeting' => __('entities.agenda.type_meeting'),
            'call' => __('entities.agenda.type_call'),
            'site_visit' => __('entities.agenda.type_site_visit'),
            'tasting' => __('entities.agenda.type_testing'),
            'showcase' => __('entities.agenda.type_showcase'),
            default => __('entities.agenda.type_other'),
        };
    }
}
