<div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scroll">
        <div class="modal-content radius-12">
            <form id="eventForm" method="POST" action="{{ route('command.agenda.store') }}">
                @csrf
                @method('POST')
                <input type="hidden" name="id" id="eventId" value="">
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold" id="eventModalTitle">{{ __('entities.agenda_events.add_event') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex flex-column gap-3">
                        <div class="row g-3">
                            <div class="col-8">
                                <label class="form-label fw-semibold text-neutral-700 text-xs text-uppercase">{{ __('entities.agenda_events.event_title') }}</label>
                                <input type="text" name="title" id="eventTitle" class="form-control radius-8" required>
                            </div>
                            <div class="col-4">
                                <label class="form-label fw-semibold text-neutral-700 text-xs text-uppercase">{{ __('entities.agenda_events.event_type') }}</label>
                                <select name="type" id="eventType" class="form-select radius-8">
                                    <option value="meeting">{{ __('entities.agenda.type_meeting') }}</option>
                                    <option value="call">{{ __('entities.agenda.type_call') }}</option>
                                    <option value="site_visit">{{ __('entities.agenda.type_site_visit') }}</option>
                                    <option value="tasting">{{ __('entities.agenda.type_testing') }}</option>
                                    <option value="showcase">{{ __('entities.agenda.type_showcase') }}</option>
                                    <option value="other">{{ __('entities.agenda.type_other') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold text-neutral-700 text-xs text-uppercase">{{ __('entities.agenda_events.start_time') }}</label>
                                <input type="datetime-local" name="start_time" id="eventStartTime" class="form-control radius-8" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold text-neutral-700 text-xs text-uppercase">{{ __('entities.agenda_events.end_time') }}</label>
                                <input type="datetime-local" name="end_time" id="eventEndTime" class="form-control radius-8">
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-8">
                                <label class="form-label fw-semibold text-neutral-700 text-xs text-uppercase">{{ __('entities.agenda.location') }}</label>
                                <input type="text" name="location" id="eventLocation" class="form-control radius-8" placeholder="e.g., Private Majlis, Ground Floor">
                            </div>
                            <div class="col-4">
                                <label class="form-label fw-semibold text-neutral-700 text-xs text-uppercase">{{ __('entities.agenda.guest_of_honor') }}</label>
                                <input type="text" name="guest_of_honor" id="eventGuest" class="form-control radius-8" placeholder="Guest of Honor">
                            </div>
                        </div>

                        <div>
                            <label class="form-label fw-semibold text-neutral-700 text-xs text-uppercase">{{ __('entities.agenda.host') }}</label>
                            <input type="text" name="host_name" id="eventHost" class="form-control radius-8">
                        </div>

                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold text-neutral-700 text-xs text-uppercase">{{ __('entities.agenda.dietary_restrictions') }}</label>
                                <textarea name="dietary_restrictions" id="eventDietary" class="form-control radius-8" rows="2"
                                    placeholder="e.g., No dairy; black tea only"></textarea>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold text-neutral-700 text-xs text-uppercase">{{ __('entities.agenda.required_materials') }}</label>
                                <input type="text" name="required_materials_input" id="eventMaterials" class="form-control radius-8"
                                    placeholder="Comma-separated: sample set #3, COA certificate">
                            </div>
                        </div>

                        <div>
                            <label class="form-label fw-semibold text-neutral-700 text-xs text-uppercase">{{ __('entities.agenda.protocol_brief') }}</label>
                            <textarea name="protocol_notes" id="eventProtocol" class="form-control radius-8" rows="3"
                                placeholder="Cultural protocols, seating, greeting procedures..."></textarea>
                        </div>

                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold text-neutral-700 text-xs text-uppercase">Client</label>
                                <select name="customer_id" id="eventClient" class="form-select radius-8">
                                    <option value="">— Select client —</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->type }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold text-neutral-700 text-xs text-uppercase">{{ __('entities.agenda_events.tags') }}</label>
                                <input type="text" name="tags_input" id="eventTags" class="form-control radius-8" placeholder="e.g., VIP, Diplomatic, Urgent">
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold text-neutral-700 text-xs text-uppercase">Priority</label>
                                <input type="hidden" name="priority" id="eventPriorityValue" value="normal">
                                <div class="d-flex gap-2 flex-wrap" id="priority-pills">
                                    <label class="btn btn-neutral-50 text-neutral-600 radius-8 d-flex align-items-center gap-2 priority-pill active" data-priority="normal">
                                        <input type="radio" name="priority-radio" value="normal" class="d-none" checked>
                                        <iconify-icon icon="solar:flag-outline"></iconify-icon>
                                        <span>{{ __('entities.agenda.priority_normal') }}</span>
                                    </label>
                                    <label class="btn btn-success-50 text-success-700 radius-8 d-flex align-items-center gap-2 priority-pill" data-priority="low">
                                        <input type="radio" name="priority-radio" value="low" class="d-none">
                                        <iconify-icon icon="solar:arrow-down-outline"></iconify-icon>
                                        <span>{{ __('entities.agenda.priority_low') }}</span>
                                    </label>
                                    <label class="btn btn-warning-50 text-warning-700 radius-8 d-flex align-items-center gap-2 priority-pill" data-priority="medium">
                                        <input type="radio" name="priority-radio" value="medium" class="d-none">
                                        <iconify-icon icon="solar:flag-outline"></iconify-icon>
                                        <span>{{ __('entities.agenda.priority_medium') }}</span>
                                    </label>
                                    <label class="btn btn-danger-50 text-danger-700 radius-8 d-flex align-items-center gap-2 priority-pill" data-priority="high">
                                        <input type="radio" name="priority-radio" value="high" class="d-none">
                                        <iconify-icon icon="solar:flag-outline"></iconify-icon>
                                        <span>{{ __('entities.agenda.priority_high') }}</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold text-neutral-700 text-xs text-uppercase">Status</label>
                                <input type="hidden" name="status" id="eventStatusValue" value="scheduled">
                                <div class="d-flex gap-2 flex-wrap" id="status-pills">
                                    <label class="btn btn-neutral-100 text-neutral-600 radius-8 d-flex align-items-center gap-2 status-pill active" data-status="scheduled">
                                        <input type="radio" name="status-radio" value="scheduled" class="d-none" checked>
                                        <iconify-icon icon="solar:calendar-outline"></iconify-icon>
                                        <span>{{ __('entities.agenda.status_scheduled') }}</span>
                                    </label>
                                    <label class="btn btn-primary-50 text-primary-700 radius-8 d-flex align-items-center gap-2 status-pill" data-status="confirmed">
                                        <input type="radio" name="status-radio" value="confirmed" class="d-none">
                                        <iconify-icon icon="solar:check-circle-outline"></iconify-icon>
                                        <span>{{ __('entities.agenda.status_confirmed') }}</span>
                                    </label>
                                    <label class="btn btn-warning-50 text-warning-700 radius-8 d-flex align-items-center gap-2 status-pill" data-status="in_progress">
                                        <input type="radio" name="status-radio" value="in_progress" class="d-none">
                                        <iconify-icon icon="solar:loader-outline"></iconify-icon>
                                        <span>{{ __('entities.agenda.status_in_progress') }}</span>
                                    </label>
                                    <label class="btn btn-success-50 text-success-700 radius-8 d-flex align-items-center gap-2 status-pill" data-status="completed">
                                        <input type="radio" name="status-radio" value="completed" class="d-none">
                                        <iconify-icon icon="solar:check-circle-outline"></iconify-icon>
                                        <span>{{ __('entities.agenda.status_completed') }}</span>
                                    </label>
                                    <label class="btn btn-danger-50 text-danger-700 radius-8 d-flex align-items-center gap-2 status-pill" data-status="cancelled">
                                        <input type="radio" name="status-radio" value="cancelled" class="d-none">
                                        <iconify-icon icon="solar:cross-circle-outline"></iconify-icon>
                                        <span>{{ __('entities.agenda.status_cancelled') }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-neutral-600 text-neutral-600 radius-8 px-20 py-11" data-bs-dismiss="modal">
                        {{ __('entities.delegations.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary-600 radius-8 px-20 py-11">{{ __('entities.agenda_events.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
