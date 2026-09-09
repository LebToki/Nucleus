@php
    $priorityClass = match ($delegation->priority) {
        'high' => 'bg-danger-50 text-danger-700',
        'medium' => 'bg-warning-50 text-warning-700',
        'low' => 'bg-success-50 text-success-700',
        default => 'bg-neutral-50 text-neutral-600',
    };

    $statusClass = match ($delegation->statusColor()) {
        'primary' => 'bg-primary-50 text-primary-700',
        'warning' => 'bg-warning-50 text-warning-700',
        'success' => 'bg-success-50 text-success-700',
        'danger' => 'bg-danger-50 text-danger-700',
        default => 'bg-neutral-100 text-neutral-600',
    };

    $isOverdue = $delegation->isOverdue();
@endphp

<div class="kanban-card bg-neutral-50 p-16 radius-8 mb-24 d-flex flex-column h-100" id="delegation-{{ $delegation->id }}">
    <div class="mb-12 d-flex align-items-start justify-content-between">
        <h6 class="kanban-title text-lg mb-0 text-neutral-900 line-clamp-1 flex-grow-1 me-2">{{ $delegation->displayName() }}</h6>
        <span class="badge {{ $statusClass }} text-xs text-uppercase flex-shrink-0">{{ $delegation->statusLabel() }}</span>
    </div>

    <div class="mb-12 d-flex align-items-center gap-2 flex-shrink-0">
        <span class="badge {{ $priorityClass }} text-xs text-uppercase">
            <iconify-icon icon="solar:flag-outline" class="me-1"></iconify-icon>
            {{ $delegation->priorityLabel() }}
        </span>
        @if ($isOverdue)
            <span class="badge bg-danger-50 text-danger-700 text-xs">
                <iconify-icon icon="solar:clock-circle-outline" class="me-1"></iconify-icon>
                {{ __('entities.delegations.overdue') }}
            </span>
        @endif
    </div>

    @if ($delegation->due_date)
        <div class="mb-12 d-flex align-items-center gap-2 text-secondary-light text-xs flex-shrink-0">
            <iconify-icon icon="solar:calendar-date-outline" class="text-primary-light"></iconify-icon>
            <span>{{ $delegation->due_date->format('M d, Y') }}</span>
        </div>
    @endif

    @if ($delegation->assignedBy)
        <div class="mb-12 d-flex align-items-center gap-2 text-secondary-light text-xs flex-shrink-0">
            <iconify-icon icon="solar:user-circle-outline"></iconify-icon>
            <span>{{ __('entities.delegations.assigned_by') }}: {{ $delegation->assignedBy->name }}</span>
        </div>
    @endif

    @if ($delegation->displayDescription())
        <p class="kanban-desc text-secondary-light text-sm mb-12 line-clamp-2 flex-grow-1">{{ $delegation->displayDescription() }}</p>
    @else
        <div class="mb-12 flex-grow-1"></div>
    @endif

    <div class="d-flex flex-column gap-2 mb-12 flex-shrink-0">
        @if ($delegation->checklist_items && is_array($delegation->checklist_items))
            @foreach ($delegation->checklist_items as $item)
                @php
                    $itemText = is_array($item) ? ($item['text'] ?? '') : $item;
                    $isChecked = is_array($item) && !empty($item['completed']);
                @endphp
                <label class="d-flex align-items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="form-check-input form-check-input-sm" {{ $isChecked ? 'checked' : '' }} disabled>
                    <span class="text-xs text-neutral-900">{{ $itemText }}</span>
                </label>
            @endforeach
        @endif
    </div>

    @if ($delegation->flag_reason)
        <div class="alert alert-warning d-flex align-items-start gap-2 py-2 mb-12 radius-8 flex-shrink-0">
            <iconify-icon icon="solar:alert-triangle-outline" class="text-warning-600 mt-0.5 flex-shrink-0"></iconify-icon>
            <div>
                <span class="text-xs fw-semibold text-warning-700 text-uppercase">{{ __('entities.delegations.flag_for_attention') }}</span>
                <p class="text-xs text-warning-800 mb-0 mt-1">{{ $delegation->displayFlagReason() }}</p>
            </div>
        </div>
    @endif

    <div class="d-flex align-items-center justify-content-between gap-10 mt-auto pt-3 border-top border-neutral-200 flex-shrink-0">
        <div class="d-flex align-items-center gap-2">
            @if ($delegation->customer_id)
                <a href="{{ route('crm.accounts') }}"
                    class="text-primary-600 text-sm" title="{{ __('entities.delegations.view_dossier') }}">
                    <iconify-icon icon="solar:user-id-outline"></iconify-icon>
                </a>
            @endif
            <button type="button" class="text-success-600 edit-delegation-btn hover-bg-success-100 w-32-px h-32-px d-flex align-items-center justify-content-center rounded-circle"
                data-id="{{ $delegation->id }}" title="Edit">
                <iconify-icon icon="lucide:edit"></iconify-icon>
            </button>
            <form method="POST" action="{{ route('command.delegations.destroy', $delegation) }}" class="delegation-delete-form">
                @csrf @method('DELETE')
                <button type="button" class="text-danger-600 delegation-delete-btn hover-bg-danger-100 w-32-px h-32-px d-flex align-items-center justify-content-center rounded-circle" title="{{ __('entities.delegations.delete') }}">
                    <iconify-icon icon="fluent:delete-24-regular"></iconify-icon>
                </button>
            </form>
        </div>
    </div>
</div>
