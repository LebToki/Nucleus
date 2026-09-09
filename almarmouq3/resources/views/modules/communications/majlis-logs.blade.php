@extends('layout.layout', ['title' => __('Communications'), 'subTitle' => __('Majlis Logs')])

@section('content')
<div class="d-flex flex-column gap-4">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <div class="fw-bold text-neutral-900 text-lg mb-1">Majlis Logs & Transcripts</div>
            <div class="text-neutral-500 text-sm">{{ __('Recorded sessions, decisions, and protocol minutes') }}</div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="text-sm text-neutral-500">{{ __('Total logs:') }} {{ $logs->count() }}</span>
        </div>
    </div>

    <div class="card radius-12 border-0 shadow-xs p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="bg-neutral-100">
                    <tr>
                        <th class="py-12 px-16 text-left">{{ __('Timestamp') }}</th>
                        <th class="py-12 px-16 text-left">{{ __('Channel') }}</th>
                        <th class="py-12 px-16 text-left">{{ __('Subject / Preview') }}</th>
                        <th class="py-12 px-16 text-end">{{ __('Recorded By') }}</th>
                        <th class="py-12 px-16 text-center">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr class="border-bottom border-neutral-200">
                            <td class="py-12 px-16">
                                <div class="text-sm text-neutral-900">{{ $log->created_at->format('Y-m-d') }}</div>
                                <div class="text-xs text-neutral-500">{{ $log->created_at->format('H:i') }}</div>
                            </td>
                            <td class="py-12 px-16">
                                <span class="badge bg-neutral-100 text-neutral-600 text-xs px-2 py-1">
                                    <iconify-icon icon="{{ $log->channel === 'whatsapp' ? 'logos:whatsapp' : 'bi:chat-dots' }}" class="me-1"></iconify-icon>
                                    {{ ucfirst($log->channel) }}
                                </span>
                            </td>
                            <td class="py-12 px-16">
                                <div class="fw-medium text-neutral-900 text-sm">{{ Str::limit($log->content, 80) }}</div>
                                @if($log->subject)
                                    <div class="text-xs text-neutral-500 mt-1">{{ $log->subject }}</div>
                                @endif
                            </td>
                            <td class="py-12 px-16 text-end">
                                <div class="text-sm text-neutral-900">{{ $log->user->name ?? __('System') }}</div>
                            </td>
                            <td class="py-12 px-16 text-center">
                                <span class="badge bg-neutral-50 text-neutral-600 text-xs px-2 py-1">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-20 text-center text-neutral-400">{{ __('No majlis logs found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
