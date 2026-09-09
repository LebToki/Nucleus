<div class="modal fade" id="addDelegationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scroll">
        <div class="modal-content radius-12">
            <form method="POST" action="{{ route('command.delegations.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">{{ __('entities.delegations.add_delegation') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex flex-column gap-3">
                        <input type="hidden" name="status" id="delegationStatus" value="not_started">
                        <div>
                            <label class="form-label fw-semibold text-neutral-700">{{ 'Title' }}</label>
                            <input type="text" name="title" class="form-control radius-8" placeholder="Delegation title" required>
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold text-neutral-700">{{ __('entities.delegations.assigned_by') }}</label>
                                <select name="priority" class="form-select radius-8">
                                    <option value="high">{{ __('entities.delegations.priority_high') }}</option>
                                    <option value="medium" selected>{{ __('entities.delegations.priority_medium') }}</option>
                                    <option value="normal">{{ __('entities.delegations.priority_normal') }}</option>
                                    <option value="low">{{ __('entities.delegations.priority_low') }}</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold text-neutral-700">{{ __('entities.delegations.due_date') }}</label>
                                <input type="date" name="due_date" class="form-control radius-8">
                            </div>
                        </div>
                        <div>
                            <label class="form-label fw-semibold text-neutral-700">{{ 'Description' }}</label>
                            <textarea name="description" class="form-control radius-8" rows="3"
                                placeholder="Detailed description of the delegation"></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <div class="form-check">
                                <input type="checkbox" name="flag_reason" value="Urgent escalation required" class="form-check-input" id="flag-attention">
                                <label class="form-label fw-semibold text-neutral-700" for="flag-attention">
                                    {{ __('entities.delegations.flag_for_attention') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline btn-sm radius-8" data-bs-dismiss="modal">
                        {{ __('entities.delegations.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm radius-8">Save Delegation</button>
                </div>
            </form>
        </div>
    </div>
</div>
