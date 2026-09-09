@extends('layout.layout', ['title' => __('Communications'), 'subTitle' => __('WhatsApp Message Detail')])

@section('content')
<div class="chat-wrapper">
    <div class="chat-sidebar card">
        <div class="chat-sidebar-single active top-profile">
            <div class="img">
                <img src="{{ asset('assets/images/chat/1.png') }}" alt="image">
            </div>
            <div class="info">
                <div class="fw-bold text-neutral-900">WhatsApp Business</div>
                <div class="text-xs text-neutral-500">Connected</div>
            </div>
        </div>
        <div class="chat-search">
            <span class="icon">
                <iconify-icon icon="iconoir:search"></iconify-icon>
            </span>
            <input type="text" name="#0" autocomplete="off" placeholder="{{ __('Search contacts...') }}">
        </div>
        <div class="chat-all-list">
            @forelse($recentConversations ?? collect() as $conv)
                <a href="{{ route('communications.whatsapp', ['contact' => $conv->sender_phone]) }}"
                   class="chat-sidebar-single d-flex align-items-center gap-3 p-12 text-decoration-none">
                    <div class="img">
                        <img src="{{ $conv->profile_image ?? asset('assets/images/chat/default.png') }}" alt="image"
                             class="w-40-px h-40-px rounded-circle">
                    </div>
                    <div class="info flex-1">
                        <div class="fw-bold text-neutral-900 text-sm">{{ $conv->sender_name }}</div>
                        <div class="text-xs text-neutral-500">{{ Str::limit($conv->content ?? '', 40) }}</div>
                    </div>
                    <div class="action text-end">
                        <div class="text-xs text-neutral-400">{{ $conv->created_at->format('H:i') }}</div>
                        <span class="w-16-px h-16-px text-xs rounded-circle bg-primary-600 text-white d-inline-flex align-items-center justify-content-center mt-4">
                            {{ $conv->unread_count ?? 0 }}
                        </span>
                    </div>
                </a>
            @empty
                <div class="p-20 text-center text-neutral-400 text-sm">{{ __('No conversations found') }}</div>
            @endforelse
        </div>
    </div>

    <div class="chat-main card">
        @if($message)
            <div class="chat-main-header">
                <div class="d-flex align-items-center gap-3 p-16 py-20 border-bottom border-neutral-200">
                    <div class="img">
                        <img src="{{ $message->profile_image ?? asset('assets/images/chat/default.png') }}"
                             alt="image" class="w-40-px h-40-px rounded-circle">
                    </div>
                    <div class="info">
                        <div class="fw-bold text-neutral-900">{{ $message->sender_name }}</div>
                        <div class="text-xs text-neutral-500">{{ $message->sender_phone }}</div>
                    </div>
                    <div class="action ms-auto">
                        <div class="btn-group">
                            <button type="button" class="text-secondary-light text-xl"
                                    data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                                <iconify-icon icon="bi:three-dots"></iconify-icon>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-lg-end border">
                                <li><a href="javascript:void(0)" class="dropdown-item">{{ __('Block') }}</a></li>
                                <li><a href="javascript:void(0)" class="dropdown-item">{{ __('Report') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="chat-main-body p-16 overflow-y-auto" style="height: calc(100vh - 280px);">
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex gap-2 max-w-70">
                        <div class="img flex-shrink-0">
                            <img src="{{ $message->profile_image ?? asset('assets/images/chat/default.png') }}"
                                 alt="image" class="w-32-px h-32-px rounded-circle">
                        </div>
                        <div class="info">
                            <div class="chat-bubble bg-neutral-100 text-neutral-900 radius-12 p-12 d-inline-block">
                                <div class="text-sm">{{ $message->content }}</div>
                                <div class="text-end">
                                    <span class="text-xs text-neutral-400 mt-1 d-block">
                                        {{ $message->created_at->format('H:i') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="chat-main-footer p-16 border-top border-neutral-200">
                <form action="{{ route('communications.whatsapp.send') }}" method="POST"
                      class="d-flex align-items-center gap-3">
                    @csrf
                    <input type="hidden" name="to" value="{{ $message->sender_phone }}">
                    <div class="d-flex align-items-center gap-2">
                        <button type="button"
                                class="btn btn-sm btn-outline-neutral-300 p-6 radius-8">
                            <iconify-icon icon="solar:paperclip-outline" class="text-xl"></iconify-image>
                        </button>
                        <button type="button"
                                class="btn btn-sm btn-outline-neutral-300 p-6 radius-8">
                            <iconify-image icon="solar:emoji-happy-outline" class="text-xl"></iconify-image>
                        </button>
                    </div>
                    <div class="flex-1">
                        <input type="text" name="message"
                               placeholder="{{ __('Type a reply...') }}"
                               class="form-control py-10 px-16 radius-12 border-neutral-300 bg-neutral-50 focus-border-primary-600"
                               required>
                    </div>
                    <button type="submit"
                            class="btn btn-primary radius-12 px-24 py-12 d-flex align-items-center justify-content-center">
                        <iconify-image icon="solar:send-plane-linear" class="text-xl"></iconify-image>
                    </button>
                </form>
            </div>
        @else
            <div class="h-100 d-flex align-items-center justify-content-center text-center">
                <div class="text-center">
                    <div
                        class="w-72-px h-72-px rounded-circle bg-neutral-100 d-flex align-items-center justify-content-center mx-auto mb-16">
                        <iconify-image icon="solar:message-text-outline" class="text-3xl text-neutral-400"></iconify-image>
                    </div>
                    <div class="text-neutral-500 fw-medium">{{ __('Select a contact to view messages') }}</div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
