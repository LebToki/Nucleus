@extends('layout.layout', ['title' => __('Communications'), 'subTitle' => __('WhatsApp')])

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
            <div class="action">
                <div class="btn-group">
                    <button type="button" class="text-secondary-light text-xl" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                        <iconify-icon icon="bi:three-dots"></iconify-icon>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-lg-end border">
                        <li>
                            <a href="javascript:void(0)" class="dropdown-item rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2">
                                <iconify-icon icon="fluent:person-32-regular"></iconify-icon> {{ __('Profile') }}
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" class="dropdown-item rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2">
                                <iconify-icon icon="carbon:settings"></iconify-icon> {{ __('Settings') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="chat-search">
            <span class="icon">
                <iconify-icon icon="iconoir:search"></iconify-icon>
            </span>
            <input type="text" name="#0" autocomplete="off" placeholder="{{ __('Search contacts...') }}">
        </div>
        <div class="chat-all-list">
            @forelse($contacts as $contact)
                <a href="{{ route('communications.whatsapp', ['contact' => $contact->sender_phone]) }}" class="chat-sidebar-single d-flex align-items-center gap-3 p-12 text-decoration-none">
                    <div class="img">
                        <img src="{{ $contact->sender->avatar ?? asset('assets/images/chat/default.png') }}" alt="image" class="w-40-px h-40-px rounded-circle">
                    </div>
                    <div class="info flex-1">
                        <div class="fw-bold text-neutral-900 text-sm">{{ $contact->sender->name ?? $contact->sender_phone }}</div>
                        <div class="text-xs text-neutral-500 text-w-100-px">{{ Str::limit($contact->content ?? '', 40) }}</div>
                    </div>
                    <div class="action text-end">
                        <div class="text-xs text-neutral-400">{{ $contact->created_at ? $contact->created_at->format('H:i') : '' }}</div>
                        <div class="w-16-px h-16-px text-xs rounded-circle bg-warning-main text-white d-inline-flex align-items-center justify-content-center mt-4">{{ $contact->unread_count ?? 0 }}</div>
                    </div>
                </a>
            @empty
                <div class="p-20 text-center text-neutral-400 text-sm">{{ __('No contacts found') }}</div>
            @endforelse
        </div>
    </div>

    <div class="chat-main card">
        @if($activeContact)
            @php
                $activeContactInfo = $contacts->firstWhere('sender_phone', $activeContact);
            @endphp
            <div class="chat-main-header">
                <div class="d-flex align-items-center gap-3 p-16 py-20 border-bottom border-neutral-200">
                    <div class="img">
                        <img src="{{ $activeContactInfo->sender->avatar ?? asset('assets/images/chat/default.png') }}" alt="image" class="w-40-px h-40-px rounded-circle">
                    </div>
                    <div class="info">
                        <div class="fw-bold text-neutral-900">{{ $activeContactInfo->sender->name ?? $activeContactInfo->sender_phone }}</div>
                        <div class="text-xs text-neutral-500">{{ $activeContactInfo->sender_phone }}</div>
                    </div>
                    <div class="action ms-auto">
                        <div class="btn-group">
                            <button type="button" class="text-secondary-light text-xl" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                                <iconify-icon icon="bi:three-dots"></iconify-icon>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-lg-end border">
                                <li><a href="javascript:void(0)" class="dropdown-item">{{ __('View Profile') }}</a></li>
                                <li><a href="javascript:void(0)" class="dropdown-item">{{ __('Block') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="chat-main-body p-16 overflow-y-auto" style="height: calc(100vh - 280px);">
                <div class="d-flex flex-column gap-3">
                    @forelse($messages as $msg)
                        @if($msg->direction === 'inbound')
                            <div class="d-flex gap-2 max-w-70 mx-0">
                                <div class="img flex-shrink-0">
                                    <img src="{{ $activeContactInfo->sender->avatar ?? asset('assets/images/chat/default.png') }}" alt="image" class="w-32-px h-32-px rounded-circle">
                                </div>
                                <div class="info">
                                    <div class="chat-bubble bg-neutral-100 text-neutral-900 radius-12 p-12 d-inline-block">
                                        <div class="text-sm">{{ $msg->content }}</div>
                                        <div class="text-end">
                                            <span class="text-xs text-neutral-400 mt-1 d-block">{{ $msg->created_at->format('H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="d-flex gap-2 max-w-70 ms-auto flex-row-reverse">
                                <div class="img flex-shrink-0">
                                    <img src="{{ auth()->user()->avatar ?? asset('assets/images/user.png') }}" alt="image" class="w-32-px h-32-px rounded-circle">
                                </div>
                                <div class="info">
                                    <div class="chat-bubble bg-primary-600 text-white radius-12 p-12 d-inline-block">
                                        <div class="text-sm">{{ $msg->content }}</div>
                                        <div class="text-end">
                                            <span class="text-xs text-neutral-200 mt-1 d-block">{{ $msg->created_at->format('H:i') }}</span>
                                            @if($msg->status === 'sent')
                                                <iconify-icon icon="mdi:check-decagram" class="text-lg text-neutral-200"></iconify-icon>
                                            @elseif($msg->status === 'delivered')
                                                <iconify-icon icon="mdi:check-all" class="text-lg text-neutral-200"></iconify-icon>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="p-20 text-center text-neutral-400 text-sm">{{ __('No messages yet. Start a conversation!') }}</div>
                    @endforelse
                </div>
            </div>
            <div class="chat-main-footer p-16 border-top border-neutral-200">
                <form action="{{ route('communications.whatsapp.send') }}" method="POST" class="d-flex align-items-center gap-3">
                    @csrf
                    <input type="hidden" name="to" value="{{ $activeContact }}">
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-neutral-300 p-6 radius-8">
                            <iconify-icon icon="solar:paperclip-outline" class="text-xl"></iconify-icon>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-neutral-300 p-6 radius-8">
                            <iconify-icon icon="solar:emoji-happy-outline" class="text-xl"></iconify-icon>
                        </button>
                    </div>
                    <div class="flex-1">
                        <input type="text" name="message" placeholder="{{ __('Type a message...') }}" class="form-control py-10 px-16 radius-12 border-neutral-300 bg-neutral-50 focus-border-primary-600" required>
                    </div>
                    <button type="submit" class="btn btn-primary radius-12 px-24 py-12 d-flex align-items-center justify-content-center">
                        <iconify-icon icon="solar:send-plane-linear" class="text-xl"></iconify-icon>
                    </button>
                </form>
            </div>
        @else
            <div class="h-100 d-flex align-items-center justify-content-center text-center">
                <div class="text-center">
                    <div class="w-72-px h-72-px rounded-circle bg-neutral-100 d-flex align-items-center justify-content-center mx-auto mb-16">
                        <iconify-icon icon="solar:message-text-outline" class="text-3xl text-neutral-400"></iconify-icon>
                    </div>
                    <div class="text-neutral-500 fw-medium">{{ __('Select a contact to start chatting') }}</div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<x-script src="assets/js/chat.js" />
@endpush
