@extends('layout.layout')

@php
    $title = __('entities.sidebar.daily_agenda');
    $subTitle = __('entities.agenda.subtitle');

    $script = '<script>
    var lang = ' . json_encode([
        'expand_all' => __('entities.agenda.expand_all'),
        'collapse_all' => __('entities.agenda.collapse_all'),
        'edit_event' => __('entities.agenda_events.edit_event'),
        'add_event' => __('entities.agenda_events.add_event'),
        'store_route' => route('command.agenda.store'),
        'delete_confirm' => __('entities.agenda.delete_confirm'),
        'delete_warning' => __('entities.agenda.delete_warning'),
        'delete' => __('entities.agenda.delete'),
        'cancel' => __('entities.agenda.cancel'),
        'delegation_delete_confirm' => __('entities.delegations.delete_confirm'),
        'delegation_delete_warning' => __('entities.delegations.delete_warning'),
        'delegation_delete' => __('entities.delegations.delete'),
    ]) . ';

    $(function() {
        var isExpanded = false;
        $("#toggle-expand-btn").on("click", function() {
            var collapseElements = $("#agenda-page .accordion-collapse");
            collapseElements.each(function() {
                var bsCollapse = bootstrap.Collapse.getInstance(this);
                if (!bsCollapse) {
                    bsCollapse = new bootstrap.Collapse(this, { toggle: false });
                }
                if (isExpanded) {
                    bsCollapse.hide();
                } else {
                    bsCollapse.show();
                }
            });
            isExpanded = !isExpanded;
            $("#toggle-expand-text").text(isExpanded ? lang.collapse_all : lang.expand_all);
        });

        $(".priority-pill").on("click", function() {
        $("#priority-pills .priority-pill").removeClass("active");
        $(this).addClass("active");
        $("#eventPriorityValue").val($(this).data("priority"));
    });

    $(".status-pill").on("click", function() {
        $("#status-pills .status-pill").removeClass("active");
        $(this).addClass("active");
        $("#eventStatusValue").val($(this).data("status"));
    });

    $(".edit-event-btn").on("click", function() {
            var id = $(this).data("id");
            $.get("/command/agenda/" + id + "/edit", function(data) {
                $("#eventId").val(data.id);
                $("#eventTitle").val(data.title || "");
                $("#eventType").val(data.type || "meeting");
                $("#eventStartTime").val(data.start_time ? data.start_time.replace(" ", "T").slice(0, 16) : "");
                $("#eventEndTime").val(data.end_time ? data.end_time.replace(" ", "T").slice(0, 16) : "");
                $("#eventLocation").val(data.location || "");
                $("#eventGuest").val(data.guest_of_honor || "");
                $("#eventHost").val(data.host_name || "");
                $("#eventDietary").val(data.dietary_restrictions || "");
                $("#eventMaterials").val(Array.isArray(data.required_materials) ? data.required_materials.join(", ") : "");
                var priority = data.priority || "normal";
                $("#priority-pills .priority-pill").removeClass("active");
                $("#priority-pills .priority-pill[data-priority='" + priority + "']").addClass("active");
                $("#eventPriorityValue").val(priority);
                $("#eventProtocol").val(data.protocol_notes || "");
                $("#eventClient").val(data.customer_id || "");
                $("#eventTags").val(Array.isArray(data.tags) ? data.tags.join(", ") : "");
                var status = data.status || "scheduled";
                $("#status-pills .status-pill").removeClass("active");
                $("#status-pills .status-pill[data-status='" + status + "']").addClass("active");
                $("#eventStatusValue").val(status);

                $("#eventForm").attr("action", "/command/agenda/" + id);
                var methodInput = $("#eventForm input[name=_method]");
                if (methodInput.length === 0) {
                    $("#eventForm").append("<input type=\"hidden\" name=\"_method\" value=\"PUT\">");
                } else {
                    methodInput.val("PUT");
                }
                $("#eventModalTitle").text(lang.edit_event);
                var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById("eventModal"));
                modal.show();
            });
        });

        $("#eventModal").on("hidden.bs.modal", function() {
            var methodInput = $("#eventForm input[name=_method]");
            if (methodInput.length) methodInput.remove();
            $("#eventForm").attr("action", lang.store_route);
            $("#eventId").val("");
            $("#eventModalTitle").text(lang.add_event);
            $("#priority-pills .priority-pill").removeClass("active");
            $("#priority-pills .priority-pill[data-priority='normal']").addClass("active");
            $("#eventPriorityValue").val("normal");
            $("#status-pills .status-pill").removeClass("active");
            $("#status-pills .status-pill[data-status='scheduled']").addClass("active");
            $("#eventStatusValue").val("scheduled");
        });
    });

    document.querySelectorAll(".delegation-delete-btn").forEach(function(btn) {
        btn.addEventListener("click", function() {
            var form = this.closest(".delegation-delete-form");
            Swal.fire({
                title: lang.delegation_delete_confirm,
                text: lang.delegation_delete_warning,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#ef4444",
                cancelButtonColor: "#6b7280",
                confirmButtonText: lang.delegation_delete,
                cancelButtonText: lang.cancel
            }).then(function(result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    document.querySelectorAll(".event-delete-btn").forEach(function(btn) {
        btn.addEventListener("click", function() {
            var form = this.closest("form");
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
</script>';
@endphp

@section('content')
<div class="d-flex flex-column gap-4" id="agenda-page">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <div class="fw-bold text-neutral-900 text-lg mb-1">{{ __('entities.agenda.title') }}</div>
            <div class="text-neutral-500 text-sm">{{ __('entities.agenda.subtitle') }}</div>
        </div>
        <div class="d-flex gap-2">
            <button type="button"
                class="btn btn-outline-neutral-600 text-neutral-600 radius-8 px-20 py-11 d-flex align-items-center gap-2"
                id="toggle-expand-btn">
                <iconify-icon icon="solar:double-alt-down-outline"></iconify-icon>
                <span id="toggle-expand-text">{{ __('entities.agenda.expand_all') }}</span>
            </button>
            <button type="button"
                class="btn rounded-pill btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2"
                data-bs-toggle="modal" data-bs-target="#eventModal" data-event-id="">
                <iconify-icon icon="ph:plus-circle"></iconify-icon>
                {{ __('entities.agenda_events.add_event') }}
            </button>
        </div>
    </div>

    @if ($todayEvents->isNotEmpty())
        <div class="card radius-12 border-0 shadow-xs">
            <div class="card-header border-0 bg-neutral-50">
                <h6 class="text-lg mb-0 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:calendar-with-timer-outline" class="text-primary-600"></iconify-icon>
                    {{ __('entities.agenda.today') }}
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="accordion accordion-flush" id="today-events-accordion">
                    @foreach ($todayEvents as $index => $event)
                        <div class="accordion-item border-0 {{ $index < $todayEvents->count() - 1 ? 'mb-2' : 'mb-0' }}">
                            <div class="accordion-header" id="today-heading-{{ $index }}">
                                <button
                                    class="accordion-button bg-transparent py-3 fw-semibold text-neutral-900 collapsed"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#today-collapse-{{ $index }}"
                                    aria-expanded="false"
                                    aria-controls="today-collapse-{{ $index }}">
                                    @include('components.command.event-summary', ['event' => $event])
                                </button>
                            </div>
                            <div id="today-collapse-{{ $index }}" class="accordion-collapse collapse"
                                aria-labelledby="today-heading-{{ $index }}"
                                data-bs-parent="#today-events-accordion">
                                <div class="accordion-body px-0 pb-0 bg-white border-top border-neutral-200">
                                    @include('components.command.event-detail', ['event' => $event])
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if ($upcomingEvents->isNotEmpty())
        <div class="card radius-12 border-0 shadow-xs">
            <div class="card-header border-0 bg-neutral-50">
                <h6 class="text-lg mb-0 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:calendar-check-outline" class="text-info-600"></iconify-icon>
                    {{ __('entities.agenda.this_week') }}
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="accordion accordion-flush" id="week-events-accordion">
                    @foreach ($upcomingEvents as $index => $event)
                        <div class="accordion-item border-0 {{ $index < $upcomingEvents->count() - 1 ? 'mb-2' : 'mb-0' }}">
                            <div class="accordion-header" id="week-heading-{{ $index }}">
                                <button
                                    class="accordion-button bg-transparent py-3 fw-semibold text-neutral-900 collapsed"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#week-collapse-{{ $index }}"
                                    aria-expanded="false"
                                    aria-controls="week-collapse-{{ $index }}">
                                    @include('components.command.event-summary', ['event' => $event])
                                </button>
                            </div>
                            <div id="week-collapse-{{ $index }}" class="accordion-collapse collapse"
                                aria-labelledby="week-heading-{{ $index }}"
                                data-bs-parent="#week-events-accordion">
                                <div class="accordion-body px-0 pb-0 bg-white border-top border-neutral-200">
                                    @include('components.command.event-detail', ['event' => $event])
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if ($todayEvents->isEmpty() && $upcomingEvents->isEmpty())
        <div class="card radius-12 border-0 shadow-xs">
            <div class="card-body py-48 text-center">
                <iconify-icon icon="solar:calendar-add-outline" class="text-4xl text-neutral-300 mb-3"></iconify-icon>
                <p class="text-neutral-500">{{ __('entities.agenda.no_events') }}</p>
            </div>
        </div>
    @endif
</div>

@include('components.command.event-modal', ['customers' => $customers ?? collect()])
@endsection
