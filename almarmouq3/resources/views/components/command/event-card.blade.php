@php
    $priorityColorClass = [
        'danger' => 'border-danger-500 bg-danger-50',
        'warning' => 'border-warning-500 bg-warning-50',
        'neutral' => 'border-neutral-300 bg-neutral-50',
    ][$event->priorityColor()] ?? 'border-neutral-300 bg-neutral-50';

    $statusColorClass = [
        'neutral' => 'text-neutral-600 bg-neutral-100',
        'primary' => 'text-primary-600 bg-primary-50',
        'warning' => 'text-warning-700 bg-warning-50',
        'success' => 'text-success-600 bg-success-50',
        'danger' => 'text-danger-600 bg-danger-50',
    ][$event->statusColor()] ?? 'text-neutral-600 bg-neutral-100';

    $isOpen = true;
@endphp

<div class="timeline-item" x-data="{ open: @entoobool($isOpen) }" x-init="$watch('open', v => $dispatch('update-expand', { open: v }))" x-show="expandAll || !expandAll">
    <div class="timeline-item-dot">
        <span class="timeline-dot" style="background-color: {{ $event->priorityColor() === 'danger' ? '#ef4444' : ($event->priorityColor() === 'warning' ? '#f59e0b' : '#6b7280') }};"></span>
    </div>
    <div class="timeline-item-content">
        <div class="d-flex flex-column gap-3">
            <div class="d-flex align-items-start justify-content-between">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <strong class="fw-semibold text-neutral-900">{{ $event->displayName() }}</strong>
                    <span class="badge text-xs {{ $statusColorClass }}">{{ $event->statusLabel() }}</span>
                    <span class="badge text-xs {{ $priorityColorClass }}">
                        <iconify-icon icon="solar:flag-outline" class="me-1"></iconify-icon>
                        {{ $event->priorityLabel() }}
                    </span>
                    <span class="text-xs text-neutral-500 bg-neutral-100 px-2 py-1 radius-4">
                        <iconify-icon icon="solar:tag-outline" class="me-1"></iconify-icon>
                        {{ $event->typeLabel() }}
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="text-end">
                        <div class="fw-semibold text-sm text-neutral-900">
                            {{ $event->start_time->format('g:i A') }}
                        </div>
                        @if ($event->end_time)
                            <div class="text-xs text-neutral-500">
                                – {{ $event->end_time->format('g:i A') }}
                            </div>
                        @endif
                    </div>
                    <button type="button" class="btn btn-ghost btn-sm radius-8"
                        @click="open = !open">
                        <iconify-icon :icon="open ? 'solar:down-outline' : 'solar:right-outline'"></iconify-icon>
                    </button>
                </div>
            </div>

            <div x-show="open" x-collapse>
                <div class="d-flex flex-column gap-3">
                    @if ($event->displayLocation())
                        <div class="flex items-start gap-2">
                            <iconify-icon icon="solar:maps-absolute-outline" class="text-neutral-400 mt-0.5"></iconify-icon>
                            <div>
                                <span class="text-xs fw-semibold text-neutral-600 text-uppercase">{{ __('entities.agenda.location') }}</span>
                                <div class="text-sm text-neutral-900">{{ $event->displayLocation() }}</div>
                            </div>
                        </div>
                    @endif

                    @if ($event->displayHost())
                        <div class="flex items-start gap-2">
                            <iconify-icon icon="solar:user-circle-outline" class="text-neutral-400 mt-0.5"></iconify-icon>
                            <div>
                                <span class="text-xs fw-semibold text-neutral-600 text-uppercase">{{ __('entities.agenda.host') }}</span>
                                <div class="text-sm text-neutral-900">{{ $event->displayHost() }}</div>
                            </div>
                        </div>
                    @endif

                    @if ($event->displayGuest())
                        <div class="flex items-start gap-2">
                            <iconify-icon icon="solar:crown-line-duotone" class="text-neutral-400 mt-0.5"></iconify-icon>
                            <div>
                                <span class="text-xs fw-semibold text-neutral-600 text-uppercase">{{ __('entities.agenda.guest_of_honor') }}</span>
                                <div class="text-sm text-neutral-900">{{ $event->displayGuest() }}</div>
                            </div>
                        </div>
                    @endif

                    @if ($event->displayProtocolNotes())
                        <div class="border-t border-neutral-200 pt-3">
                            <h6 class="fw-semibold text-neutral-900 mb-2">
                                <iconify-icon icon="solar:info-circle-outline" class="me-2"></iconify-icon>
                                {{ __('entities.agenda.protocol_brief') }}
                            </h6>
                            <div class="prose text-sm text-neutral-700 mb-3">
                                {!! nl2br(e($event->displayProtocolNotes())) !!}
                            </div>
                        </div>
                    @endif

                    @if ($event->displayDietary())
                        <div class="flex items-start gap-2">
                            <iconify-icon icon="solar:food-outline" class="text-neutral-400 mt-0.5"></iconify-icon>
                            <div>
                                <span class="text-xs fw-semibold text-neutral-600 text-uppercase">{{ __('entities.agenda.dietary_restrictions') }}</span>
                                <div class="text-sm text-neutral-900">{{ $event->displayDietary() }}</div>
                            </div>
                        </div>
                    @endif

                    @if ($event->required_materials && is_array($event->required_materials))
                        <div class="flex items-start gap-2">
                            <iconify-icon icon="solar:box-outline" class="text-neutral-400 mt-0.5"></iconify-icon>
                            <div>
                                <span class="text-xs fw-semibold text-neutral-600 text-uppercase">{{ __('entities.agenda.required_materials') }}</span>
                                <ul class="list-none mt-1 space-y-1">
                                    @foreach ($event->required_materials as $material)
                                        <li class="text-sm text-neutral-900">
                                            <iconify-icon icon="solar:check-circle-outline" class="text-success-600 me-1"></iconify-icon>
                                            {{ $material }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    @if ($event->checklist_items && is_array($event->checklist_items))
                        <div class="border-t border-neutral-200 pt-3">
                            <h6 class="fw-semibold text-neutral-900 mb-2">
                                <iconify-icon icon="solar:list-check-outline" class="me-2"></iconify-icon>
                                {{ __('entities.agenda.preparation_checklist') }}
                            </h6>
                            <div class="d-flex flex-column gap-2">
                                @foreach ($event->checklist_items as $item)
                                    <label class="d-flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox"
                                            {{ isset($item['completed']) && $item['completed'] ? 'checked' : '' }}
                                            class="form-check-input"
                                            wire:change="toggleChecklist({{ $event->id }}, '{{ $item['text'] ?? '' }}')">
                                        <span class="text-sm text-neutral-900">{{ $item['text'] ?? $item }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($event->customer_id)
                        <div class="d-flex gap-2 pt-2">
                            <a href="{{ route('crm.accounts') }}"
                                class="btn btn-outline btn-sm radius-8">
                                <iconify-icon icon="solar:user-id-outline" class="me-1"></iconify-icon>
                                {{ __('entities.agenda.view_dossier') }}
                            </a>
                        </div>
                    @endif

                    @if (!$event->prep_done)
                        <div class="d-flex justify-content-end pt-2">
                            <button type="button"
                                class="btn btn-success btn-sm radius-8">
                                <iconify-icon icon="solar:check-circle-outline" class="me-1"></iconify-icon>
                                {{ __('entities.agenda.mark_prep_done') }}
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
