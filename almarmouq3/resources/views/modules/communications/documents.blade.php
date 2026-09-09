@extends('layout.layout', ['title' => __('Communications'), 'subTitle' => __('Client Vaults')])

@section('content')
<div class="d-flex flex-column gap-4">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <div class="fw-bold text-neutral-900 text-lg mb-1">Client Document Vaults</div>
            <div class="text-neutral-500 text-sm">{{ __('Certificates, client dossiers, and verified archives') }}</div>
        </div>
            <button type="button" class="btn btn-primary radius-8 px-24 py-12 d-flex align-items-center gap-2">
                <iconify-icon icon="solar:upload-file-outline"></iconify-icon>
                {{ __('Upload Document') }}
            </button>
    </div>

    <div class="row g-3">
        @forelse($documents as $doc)
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100 p-20 radius-12 border-0 shadow-xs">
                    <div class="d-flex flex-column items-center text-center gap-3">
                        <div class="w-48-px h-48-px rounded-8 bg-neutral-100 d-flex align-items-center justify-content-center">
                            <iconify-icon icon="solar:file-text-outline" class="text-2xl text-neutral-600"></iconify-icon>
                        </div>
                        <div class="flex-1">
                            <div class="fw-bold text-neutral-900 text-sm mb-1">{{ Str::limit($doc->subject, 30) }}</div>
                            <div class="text-xs text-neutral-500">{{ $doc->created_at->format('Y-m-d') }}</div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-neutral-100 text-neutral-600 text-xs px-2 py-1">
                                {{ Str::limit($doc->file_size_formatted ?? '', 10) }}
                            </span>
                        </div>
                        <a href="javascript:void(0)"
                           class="btn btn-sm btn-outline-neutral-300 w-full radius-8">
                            <iconify-icon icon="solar:eye-outline"></iconify-icon> {{ __('View') }}
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card p-40 radius-12 border-0 shadow-xs text-center">
                    <div class="w-64-px h-64-px rounded-circle bg-neutral-100 d-flex align-items-center justify-content-center mx-auto mb-16">
                        <iconify-icon icon="solar:folder-with-files-outline" class="text-3xl text-neutral-400"></iconify-icon>
                    </div>
                    <div class="text-neutral-500 fw-medium">{{ __('No documents in vault yet') }}</div>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
