@extends('layout.layout')

@php
    $title = __('entities.sidebar.delegations');
    $subTitle = __('entities.delegations.subtitle');

    $columns = [
        'not_started' => [
            'label' => __('entities.delegations.kanban_not_started'),
            'bg' => 'neutral-50',
            'text' => 'text-neutral-600',
        ],
        'in_progress' => [
            'label' => __('entities.delegations.kanban_in_progress'),
            'bg' => 'warning-50',
            'text' => 'text-warning-700',
        ],
        'awaiting_approval' => [
            'label' => __('entities.delegations.kanban_awaiting'),
            'bg' => 'primary-50',
            'text' => 'text-primary-700',
        ],
        'blocked' => [
            'label' => __('entities.delegations.kanban_blocked'),
            'bg' => 'danger-50',
            'text' => 'text-danger-700',
        ],
        'completed' => [
            'label' => __('entities.delegations.kanban_completed'),
            'bg' => 'success-50',
            'text' => 'text-success-700',
        ],
    ];

    $script = '<script>
        var lang = ' . json_encode([
            'delete_confirm' => __('entities.delegations.delete_confirm'),
            'delete_warning' => __('entities.delegations.delete_warning'),
            'delete' => __('entities.delegations.delete'),
            'cancel' => __('entities.delegations.cancel'),
        ]) . ';

        $(function() {
            $("#sortable-wrapper").sortable();
            $(".connectedSortable").sortable({
                connectWith: ".connectedSortable",
                placeholder: "kanban-card-placeholder",
                helper: "clone",
                scroll: false,
                update: function(event, ui) {
                    var newStatus = $(this).attr("id").replace("sortable-", "");
                    var taskId = ui.item.attr("id").replace("delegation-", "");
                    $.ajax({
                        url: "/command/delegations/" + taskId + "/status",
                        method: "POST",
                        data: {
                            _token: $("meta[name=csrf-token]").attr("content"),
                            status: newStatus
                        },
                        success: function() {
                            location.reload();
                        }
                    });
                }
            }).disableSelection();

            document.querySelectorAll(".add-delegation").forEach(function(btn) {
                btn.addEventListener("click", function() {
                    var status = this.getAttribute("data-status");
                    document.getElementById("delegationStatus").value = status;
                    var modal = new bootstrap.Modal(document.getElementById("addDelegationModal"));
                    modal.show();
                });
            });

            document.querySelectorAll(".delegation-delete-btn").forEach(function(btn) {
                btn.addEventListener("click", function() {
                    var form = this.closest(".delegation-delete-form");
                    Swal.fire({
                        title: lang.delete_confirm,
                        text: lang.delete_warning,
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#ef4444",
                        cancelButtonColor: "#6b7280",
                        confirmButtonText: lang.delete,
                        cancelButtonText: lang.cancel
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>';
@endphp

@section('content')
    <div class="d-flex flex-column gap-4">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <div class="fw-bold text-neutral-900 text-lg mb-1">{{ __('entities.delegations.title') }}</div>
                <div class="text-neutral-500 text-sm">{{ __('entities.delegations.subtitle') }}</div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show radius-8 mb-0" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="overflow-x-auto scroll-sm">
            <div class="kanban-wrapper">
                <div class="d-flex align-items-start gap-24" id="sortable-wrapper">
                    @foreach ($columns as $statusKey => $col)
                        @php
                            $columnDelegations = $delegations->where('status', $statusKey);
                        @endphp
                        <div class="w-25 kanban-item radius-12" style="min-width: 240px;">
                            <div class="card p-0 radius-12 overflow-hidden shadow-none">
                                <div class="card-body p-0 pb-24">
                                    <div class="d-flex align-items-center gap-2 justify-content-between ps-24 pt-24 pe-24">
                                        <h6 class="text-lg mb-0 {{ $col['text'] }}">{{ $col['label'] }}</h6>
                                        <button type="button" class="add-delegation text-2xl hover-text-primary"
                                            data-status="{{ $statusKey }}"
                                            title="{{ __('entities.delegations.add_delegation') }}">
                                            <iconify-icon icon="ph:plus-circle"></iconify-icon>
                                        </button>
                                    </div>

                                    <div class="connectedSortable d-flex flex-column gap-24 align-items-stretch flex-grow-1"
                                        id="sortable-{{ $statusKey }}">
                                        @forelse ($columnDelegations as $delegation)
                                            @include('components.command.delegation-card', [
                                                'delegation' => $delegation,
                                            ])
                                        @empty
                                            <div class="text-center py-12 text-neutral-300 text-xs">
                                                {{ __('entities.delegations.no_delegations') }}
                                            </div>
                                        @endforelse
                                    </div>

                                    <button type="button"
                                        class="d-flex align-items-center gap-2 fw-medium w-100 text-neutral-600 justify-content-center text-hover-neutral-900 line-height-1 ps-24 pt-12 pb-12 add-delegation"
                                        data-status="{{ $statusKey }}">
                                        <iconify-icon icon="ph:plus-circle" class="icon text-xl"></iconify-icon>
                                        {{ __('entities.delegations.add_delegation') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @include('components.command.delegation-modal')
@endsection

