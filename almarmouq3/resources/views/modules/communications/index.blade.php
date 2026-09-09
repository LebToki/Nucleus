@extends('layout.layout')

@section('content')
    <div class="d-flex flex-column gap-4">

        {{-- Section Header --}}
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h4 class="fw-bold mb-1">Communications & Protocol Cockpit</h4>
                <p class="text-neutral-500 text-sm mb-0">VIP messaging traffic, Majlis minutes, SLA tracking & client
                    document vaults</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-neutral-200 text-neutral-800 px-3 py-2 radius-8">
                    <iconify-icon icon="solar:shield-check-outline" class="me-1 align-middle text-success-600"></iconify-icon>
                    Encrypted Channels Active
                </span>
            </div>
        </div>

        {{-- Tier 1: 4 KPI Cards --}}
        <div class="row g-3">
            {{-- Card 1: Active Concierge Threads --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100 p-20 radius-12 border-0" style="background-color: #FFF7ED;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-xs fw-semibold text-warning-700 text-uppercase">Concierge Channels</span>
                            <h3 class="fw-bold text-neutral-900 mt-2 mb-1">14</h3>
                            <span class="text-xs text-neutral-500">Live WhatsApp & VIP threads</span>
                        </div>
                        <div
                            class="w-40-px h-40-px rounded-circle d-flex align-items-center justify-content-center text-warning-700 bg-white shadow-xs">
                            <iconify-icon icon="solar:chat-round-dots-outline" class="text-xl"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Client Document Vaults --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100 p-20 radius-12 border-0" style="background-color: #ECFDF5;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-xs fw-semibold text-teal-700 text-uppercase">Client Vaults</span>
                            <h3 class="fw-bold text-neutral-900 mt-2 mb-1">42</h3>
                            <span class="text-xs text-neutral-500">Active dossiers & verified archives</span>
                        </div>
                        <div
                            class="w-40-px h-40-px rounded-circle d-flex align-items-center justify-content-center text-teal-700 bg-white shadow-xs">
                            <iconify-icon icon="solar:folder-with-files-outline" class="text-xl"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 3: Inbound / Resolved Messages (Inflow) --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100 p-20 radius-12 border-0" style="background-color: #F0FDF4;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-xs fw-semibold text-success-700 text-uppercase">Dispatches Resolved</span>
                            <h3 class="fw-bold text-success-600 mt-2 mb-1">168</h3>
                            <span class="text-xs text-neutral-500">Average response: 4m 12s</span>
                        </div>
                        <div
                            class="w-40-px h-40-px rounded-circle d-flex align-items-center justify-content-center text-success-600 bg-white shadow-xs">
                            <iconify-icon icon="solar:check-read-outline" class="text-xl"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 4: Open Inquiries & Awaiting Docs (Outflow / Pending) --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100 p-20 radius-12 border-0" style="background-color: #FEF2F2;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-xs fw-semibold text-danger-700 text-uppercase">Pending Inquiries</span>
                            <h3 class="fw-bold text-danger-600 mt-2 mb-1">7</h3>
                            <span class="text-xs text-neutral-500">Require curator or protocol response</span>
                        </div>
                        <div
                            class="w-40-px h-40-px rounded-circle d-flex align-items-center justify-content-center text-danger-600 bg-white shadow-xs">
                            <iconify-icon icon="solar:bell-bing-outline" class="text-xl"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tier 2: 2 Velocity Curves --}}
        <div class="row g-3">
            <div class="col-12 col-lg-6">
                <div class="card p-24 radius-12 border-0 shadow-xs h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-semibold mb-0">Protocol Interaction Velocity</h6>
                        <span class="text-xs text-neutral-400">WhatsApp vs Webmail (Weekly)</span>
                    </div>
                    <div id="chart-comm-traffic" style="min-height: 220px;"></div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card p-24 radius-12 border-0 shadow-xs h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-semibold mb-0">Document Ingestion & Vault Transfers</h6>
                        <span class="text-xs text-neutral-400">Certificates & Client Dossiers</span>
                    </div>
                    <div id="chart-doc-vaults" style="min-height: 220px;"></div>
                </div>
            </div>
        </div>

        {{-- Tier 3: 2 Telemetry Feeds --}}
        <div class="row g-3">
            {{-- Feed 1: Recent Interactions & Majlis Transcripts --}}
            <div class="col-12 col-lg-6">
                <div class="card p-20 radius-12 border-0 shadow-xs">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-semibold mb-0">Recent Inbound Communications</h6>
                        <span class="badge bg-neutral-100 text-neutral-600 text-xs px-2 py-1">Last 5 Days</span>
                    </div>
                    <div class="d-flex flex-column gap-2 overflow-y-auto pe-1" style="max-height: 380px;">
                        @forelse ($recentMessages ?? [] as $msg)
                            <div
                                class="p-12 radius-8 border border-neutral-200 d-flex justify-content-between align-items-center hover-bg-neutral-50 transition-all">
                                <div class="d-flex align-items-center gap-3">
                                    <div
                                        class="w-36-px h-36-px rounded-circle d-flex align-items-center justify-content-center bg-primary-50 text-primary-600 flex-shrink-0">
                                        <iconify-icon icon="{{ $msg->channel_icon ?? 'solar:chat-round-dots-outline' }}"
                                            class="text-lg"></iconify-icon>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-neutral-900 text-sm">{{ $msg->sender->name ?? $msg->recipient ?? __('Unknown') }}</div>
                                        <div class="text-xs text-neutral-500 mt-1">{{ Str::limit($msg->preview_text ?? $msg->content ?? '', 45) }}
                                        </div>
                                        <div class="text-xs text-neutral-400 mt-1">{{ $msg->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                                <span
                                    class="badge {{ $msg->status === 'unread' ? 'bg-danger-50 text-danger-600' : 'bg-neutral-100 text-neutral-600' }} text-xs px-2 py-1">
                                    {{ $msg->status }}
                                </span>
                            </div>
                        @empty
                            <div class="p-20 text-center text-neutral-400 text-sm">No recent communication records.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Feed 2: Client Vault Files & Uploads --}}
            <div class="col-12 col-lg-6">
                <div class="card p-20 radius-12 border-0 shadow-xs">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-semibold mb-0">Client Document Vault Ingestion</h6>
                        <span class="badge bg-neutral-100 text-neutral-600 text-xs px-2 py-1">Client Folders</span>
                    </div>
                    <div class="d-flex flex-column gap-2 overflow-y-auto pe-1" style="max-height: 380px;">
                        @forelse ($recentDocuments ?? [] as $doc)
                            <div
                                class="p-12 radius-8 border border-neutral-200 d-flex justify-content-between align-items-center hover-bg-neutral-50 transition-all">
                                <div class="d-flex align-items-center gap-3">
                                    <div
                                        class="w-36-px h-36-px rounded-8 d-flex align-items-center justify-content-center bg-neutral-100 text-neutral-700 flex-shrink-0">
                                        <iconify-icon icon="solar:file-text-outline" class="text-xl"></iconify-icon>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-neutral-900 text-sm">{{ $doc->file_name }}</div>
                                        <div class="text-xs text-neutral-500 mt-1">Folder:
                                            <strong>{{ $doc->client_name }}</strong> &bull; {{ $doc->file_size_formatted }}
                                        </div>
                                        <div class="text-xs text-neutral-400 mt-1">Uploaded:
                                            {{ $doc->created_at->format('Y-m-d H:i') }}</div>
                                    </div>
                                </div>
                                <a href="{{ route('communications.documents.show', $doc->id) }}"
                                    class="btn btn-sm btn-outline-neutral-300 p-6 radius-6">
                                    <iconify-icon icon="solar:eye-outline" class="text-base align-middle"></iconify-icon>
                                </a>
                            </div>
                        @empty
                            <div class="p-20 text-center text-neutral-400 text-sm">No recent documents uploaded.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
