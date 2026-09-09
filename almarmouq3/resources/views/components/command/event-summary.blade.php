@php
    $priorityBg = match ($event->priority) {
        'high' => 'bg-danger-50 text-danger-700',
        'medium' => 'bg-warning-50 text-warning-700',
        'low' => 'bg-success-50 text-success-700',
        default => 'bg-neutral-50 text-neutral-600',
    };

    $statusBg = match ($event->statusColor()) {
        'primary' => 'bg-primary-50 text-primary-700',
        'warning' => 'bg-warning-50 text-warning-700',
        'success' => 'bg-success-50 text-success-700',
        'danger' => 'bg-danger-50 text-danger-700',
        default => 'bg-neutral-100 text-neutral-600',
    };
@endphp

<div class="d-flex align-items-center gap-3 flex-grow-1 min-width-0">
    <div class="d-flex align-items-center gap-2 flex-nowrap">
        <div class="text-center" style="min-width: 70px;">
            <div class="fw-bold text-sm text-neutral-900">{{ $event->start_time->format('g:i A') }}</div>
            @if ($event->end_time)
                <div class="text-xs text-neutral-500">– {{ $event->end_time->format('g:i A') }}</div>
            @endif
        </div>
        <span class="badge {{ $statusBg }} d-flex align-items-center gap-2">{{ $event->statusLabel() }}</span>
        <span class="badge {{ $priorityBg }} d-flex align-items-center gap-2">
            <iconify-icon icon="solar:flag-outline"></iconify-icon>
            {{ $event->priorityLabel() }}
        </span>
        <span class="badge bg-neutral-100 text-neutral-600 radius-4 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:tag-outline"></iconify-icon>
            {{ $event->typeLabel() }}
        </span>
        @if ($event->tags)
            @foreach ($event->tags as $tag)
                <span class="badge bg-info-50 text-info-700 d-flex align-items-center gap-2">{{ $tag }}</span>
            @endforeach
        @endif
    </div>
    <strong class="fw-semibold text-neutral-900 text-truncate flex-grow-1">{{ $event->displayName() }}</strong>
    <div class="d-flex align-items-center gap-1 flex-shrink-0">
        @if ($event->customer)
            <a href="{{ route('crm.accounts') }}"
                class="text-primary-600 text-sm" title="{{ __('entities.agenda.view_dossier') }}">
                <iconify-icon icon="solar:user-id-outline"></iconify-icon>
            </a>
        @endif
        <button type="button" class="text-neutral-400 hover-text-primary-600 edit-event-btn"
            data-id="{{ $event->id }}" title="{{ __('entities.agenda_events.edit_event') }}">
            <iconify-icon icon="lucide:edit"></iconify-icon>
        </button>
    </div>
</div>
