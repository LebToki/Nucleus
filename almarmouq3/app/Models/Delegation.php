<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Delegation extends Model
{
    protected $fillable = [
        'assignee_id',
        'assigned_by_id',
        'customer_id',
        'task_name',
        'task_name_ar',
        'description',
        'description_ar',
        'priority',
        'status',
        'due_date',
        'checklist_items',
        'attachments',
        'flag_reason',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'checklist_items' => 'array',
            'attachments' => 'array',
        ];
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(DelegationComment::class);
    }

    public function displayName(): string
    {
        return app()->getLocale() === 'ar' && $this->task_name_ar
            ? $this->task_name_ar
            : $this->task_name;
    }

    public function displayFlagReason(): ?string
    {
        if (app()->getLocale() === 'ar' && $this->flag_reason_ar) {
            return $this->flag_reason_ar;
        }
        return $this->flag_reason;
    }

    public function displayDescription(): ?string
    {
        if (app()->getLocale() === 'ar' && $this->description_ar) {
            return $this->description_ar;
        }
        return $this->description;
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
            'not_started' => __('entities.agenda.status_not_started'),
            'in_progress' => __('entities.agenda.status_in_progress'),
            'awaiting_approval' => __('entities.agenda.status_awaiting_approval'),
            'completed' => __('entities.agenda.status_completed'),
            'blocked' => __('entities.agenda.status_blocked'),
            default => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'not_started' => 'neutral',
            'in_progress' => 'warning',
            'awaiting_approval' => 'primary',
            'completed' => 'success',
            'blocked' => 'danger',
            default => 'neutral',
        };
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date < now() && !in_array($this->status, ['completed', 'blocked']);
    }
}
