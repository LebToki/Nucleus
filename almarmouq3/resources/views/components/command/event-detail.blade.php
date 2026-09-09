@php
    $priorityColorClass = match ($event->priorityColor()) {
        'danger' => 'bg-danger-100 text-danger-700',
        'warning' => 'bg-warning-100 text-warning-700',
        default => 'bg-neutral-100 text-neutral-600',
    };

    $statusColorClass = match ($event->statusColor()) {
        'neutral' => 'text-neutral-600 bg-neutral-100',
        'primary' => 'text-primary-600 bg-primary-50',
        'warning' => 'text-warning-700 bg-warning-50',
        'success' => 'text-success-600 bg-success-50',
        'danger' => 'text-danger-600 bg-danger-50',
        default => 'text-neutral-600 bg-neutral-100',
    };
@endphp

<div class="p-4">
    @if ($event->displayLocation())
        <div class="d-flex align-items-start gap-2 mb-3">
            <iconify-icon icon="solar:maps-absolute-outline" class="text-neutral-400 mt-0.5"></iconify-icon>
            <div>
                <span class="text-xs fw-semibold text-neutral-600 text-uppercase">{{ __('entities.agenda.location') }}</span>
                <div class="text-sm text-neutral-900">{{ $event->displayLocation() }}</div>
            </div>
        </div>
    @endif

    @if ($event->displayHost())
        <div class="d-flex align-items-start gap-2 mb-3">
            <iconify-icon icon="solar:user-circle-outline" class="text-neutral-400 mt-0.5"></iconify-icon>
            <div>
                <span class="text-xs fw-semibold text-neutral-600 text-uppercase">{{ __('entities.agenda.host') }}</span>
                <div class="text-sm text-neutral-900">{{ $event->displayHost() }}</div>
            </div>
        </div>
    @endif

    @if ($event->displayGuest())
        <div class="d-flex align-items-start gap-2 mb-3">
            <iconify-icon icon="solar:crown-line-duotone" class="text-neutral-400 mt-0.5"></iconify-icon>
            <div>
                <span class="text-xs fw-semibold text-neutral-600 text-uppercase">{{ __('entities.agenda.guest_of_honor') }}</span>
                <div class="text-sm text-neutral-900">{{ $event->displayGuest() }}</div>
            </div>
        </div>
    @endif

    @if ($event->displayProtocolNotes())
        <div class="border-top border-neutral-200 pt-3 mb-3">
            <h6 class="fw-semibold text-neutral-900 mb-2 d-flex align-items-center gap-2">
                <iconify-icon icon="solar:info-circle-outline" class="text-neutral-400"></iconify-icon>
                {{ __('entities.agenda.protocol_brief') }}
            </h6>
            <div class="text-sm text-neutral-700 whitespace-pre-line">{{ $event->displayProtocolNotes() }}</div>
        </div>
    @endif

    @if ($event->displayDietary())
        <div class="d-flex align-items-start gap-2 mb-3">
            <iconify-icon icon="solar:food-outline" class="text-neutral-400 mt-0.5"></iconify-icon>
            <div>
                <span class="text-xs fw-semibold text-neutral-600 text-uppercase">{{ __('entities.agenda.dietary_restrictions') }}</span>
                <div class="text-sm text-neutral-900">{{ $event->displayDietary() }}</div>
            </div>
        </div>
    @endif

    @if ($event->required_materials && is_array($event->required_materials))
        <div class="d-flex align-items-start gap-2 mb-3">
            <iconify-icon icon="solar:box-outline" class="text-neutral-400 mt-0.5"></iconify-icon>
            <div>
                <span class="text-xs fw-semibold text-neutral-600 text-uppercase">{{ __('entities.agenda.required_materials') }}</span>
                <ul class="list-unstyled mt-1 mb-0">
                    @foreach ($event->required_materials as $material)
                        <li class="d-flex align-items-center gap-1 text-sm text-neutral-900">
                            <iconify-icon icon="solar:check-circle-outline" class="text-success-600"></iconify-icon>
                            {{ $material }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if ($event->checklist_items && is_array($event->checklist_items))
        <div class="border-top border-neutral-200 pt-3 mb-3">
            <h6 class="fw-semibold text-neutral-900 mb-2 d-flex align-items-center gap-2">
                <iconify-icon icon="solar:list-check-outline" class="text-neutral-400"></iconify-icon>
                {{ __('entities.agenda.preparation_checklist') }}
            </h6>
            <div class="d-flex flex-column gap-2">
                @foreach ($event->checklist_items as $item)
                    @php
                        $itemText = is_array($item) ? ($item['text'] ?? '') : $item;
                        $isChecked = is_array($item) && !empty($item['completed']);
                    @endphp
                    <label class="d-flex align-items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="form-check-input" {{ $isChecked ? 'checked' : '' }} disabled>
                        <span class="text-sm text-neutral-900">{{ $itemText }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-neutral-200 mt-3">
        <div class="d-flex align-items-center gap-2">
            @if ($event->customer_id)
                <a href="{{ route('crm.accounts') }}"
                    class="btn btn-outline btn-sm radius-8">
                    <iconify-icon icon="solar:user-id-outline" class="me-1"></iconify-icon>
                    {{ __('entities.agenda.view_dossier') }}
                </a>
            @endif
            <form method="POST" action="{{ route('command.agenda.destroy', $event->id) }}" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="button"
                    class="btn btn-outline-danger btn-sm radius-8 event-delete-btn">
                    <iconify-icon icon="solar:trash-bin-minimalistic-outline" class="me-1"></iconify-icon>
                    {{ __('entities.agenda.delete_event') }}
                </button>
            </form>
        </div>
        @if (!$event->prep_done)
            <button type="button"
                class="btn btn-success btn-sm radius-8">
                <iconify-icon icon="solar:check-circle-outline" class="me-1"></iconify-icon>
                {{ __('entities.agenda.mark_prep_done') }}
            </button>
        @else
            <span class="badge bg-success-50 text-success-700 d-flex align-items-center gap-2">
                <iconify-icon icon="solar:check-circle-outline"></iconify-icon>
                {{ __('entities.agenda.prep_done') }}
            </span>
        @endif
    </div>
</div>
