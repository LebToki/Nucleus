@extends('layout.layout', ['title' => __('Communications'), 'subTitle' => __('Webmail')])

@section('content')
<div class="d-flex flex-column gap-4">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <div class="fw-bold text-neutral-900 text-lg mb-1">Webmail Center</div>
            <div class="text-neutral-500 text-sm">{{ __('VIP correspondence, supplier updates & protocol memos') }}</div>
        </div>
        <button type="button" class="btn btn-primary radius-8 px-24 py-12 d-flex align-items-center gap-2">
            <iconify-icon icon="solar:mail-add-outline"></iconify-icon>
            {{ __('Compose') }}
        </button>
    </div>

    <div class="card radius-12 border-0 shadow-xs p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="bg-neutral-100">
                    <tr>
                        <th class="py-12 px-16 text-left">{{ __('From / To') }}</th>
                        <th class="py-12 px-16 text-left">{{ __('Subject') }}</th>
                        <th class="py-12 px-16 text-left">{{ __('Channel') }}</th>
                        <th class="py-12 px-16 text-end">{{ __('Date') }}</th>
                        <th class="py-12 px-16 text-center">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($emails as $email)
                        <tr class="border-bottom border-neutral-200">
                            <td class="py-12 px-16">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="w-36-px h-36-px rounded-circle bg-primary-50 d-flex align-items-center justify-content-center flex-shrink-0">
                                        <iconify-icon icon="mage:email" class="text-primary-600 text-lg"></iconify-icon>
                                    </div>
                                    <div>
                                        <div class="fw-medium text-neutral-900 text-sm">{{ $email->sender->name ?? $email->recipient }}</div>
                                        <div class="text-xs text-neutral-500">{{ $email->recipient ?: '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-12 px-16">
                                <div class="fw-medium text-neutral-900 text-sm">{{ Str::limit($email->content, 60) }}</div>
                            </td>
                            <td class="py-12 px-16">
                                <span class="badge bg-neutral-100 text-neutral-600 text-xs px-2 py-1">
                                    <iconify-icon icon="mage:email" class="me-1"></iconify-icon>
                                    {{ ucfirst($email->channel) }}
                                </span>
                            </td>
                            <td class="py-12 px-16 text-end text-sm text-neutral-500">
                                {{ $email->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="py-12 px-16 text-center">
                                @if($email->status === 'read')
                                    <span class="badge bg-success-50 text-success-600 text-xs px-2 py-1">{{ __('Read') }}</span>
                                @elseif($email->status === 'unread')
                                    <span class="badge bg-danger-50 text-danger-600 text-xs px-2 py-1">{{ __('New') }}</span>
                                @else
                                    <span class="badge bg-neutral-100 text-neutral-600 text-xs px-2 py-1">{{ ucfirst($email->status) }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-20 text-center text-neutral-400">{{ __('No emails found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
