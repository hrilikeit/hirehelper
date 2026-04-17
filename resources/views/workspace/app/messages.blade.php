@extends('workspace.layouts.base', ['activeNav' => 'messages'])

@section('content')
<div class="container">
    @include('workspace.partials.flash')

    <div class="page-heading">
        <div>
            <h1>Messages</h1>
            <p>The client inbox keeps all project communication, status updates, and hiring actions in one quieter workspace.</p>
        </div>
    </div>

    <div class="split-layout">
        <section class="project-card" style="display:flex;flex-direction:column;padding:0;overflow:hidden">
            @if ($offer)
                {{-- Header --}}
                <div style="padding:22px 28px;border-bottom:1px solid var(--line);display:flex;align-items:center;gap:14px">
                    <img src="{{ $offer->freelancer_display_avatar_url }}" alt="{{ $offer->freelancer_display_name }}" style="width:42px;height:42px;border-radius:50%;object-fit:cover;border:1px solid #e5e7eb">
                    <div>
                        <strong style="font-size:16px;display:block">{{ $offer->freelancer_display_name }}</strong>
                        <span style="font-size:13px;color:var(--muted)">{{ $offer->role }}</span>
                    </div>
                    <span style="margin-left:auto;font-size:12px;padding:4px 10px;border-radius:999px;background:{{ $offer->status === 'active' ? '#dcfce7' : '#fef9c3' }};color:{{ $offer->status === 'active' ? '#166534' : '#854d0e' }};font-weight:600">{{ ucfirst($offer->status) }}</span>
                </div>

                {{-- Messages area --}}
                <div id="chat-scroll" style="flex:1;overflow-y:auto;padding:20px 28px;display:flex;flex-direction:column;gap:4px;max-height:520px;min-height:300px">
                    {{-- System note --}}
                    <div style="text-align:center;margin:8px 0 16px">
                        <span style="display:inline-block;font-size:12px;color:var(--muted);background:#f4f7ff;padding:6px 14px;border-radius:999px">
                            @if ($offer->status === 'active')
                                This room is live — use it for updates and check-ins
                            @else
                                Offer pending — you can message before activating
                            @endif
                        </span>
                    </div>

                    @php $prevMessage = null; @endphp
                    @forelse ($messages as $message)
                        @php
                            $isClient = $message->sender_type === 'client';
                            $isSystem = $message->sender_type === 'system';
                            $showHeader = ! $prevMessage
                                || $prevMessage->sender_type !== $message->sender_type
                                || optional($message->sent_at)->diffInMinutes(optional($prevMessage->sent_at)) > 5;
                        @endphp

                        @if ($isSystem)
                            <div style="text-align:center;margin:12px 0">
                                <span style="font-size:12px;color:var(--muted);background:#f8f9fc;padding:5px 12px;border-radius:999px">{{ $message->message }}</span>
                            </div>
                        @else
                            <div style="display:flex;flex-direction:column;{{ $isClient ? 'align-items:flex-end' : 'align-items:flex-start' }};{{ $showHeader ? 'margin-top:12px' : 'margin-top:2px' }}">
                                @if ($showHeader)
                                    <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;{{ $isClient ? 'flex-direction:row-reverse' : '' }}">
                                        <span style="font-size:12px;font-weight:700;color:var(--text)">{{ $message->sender_name }}</span>
                                        <span style="font-size:11px;color:var(--muted)">{{ optional($message->sent_at)->format('M j, g:i A') ?: 'Just now' }}</span>
                                    </div>
                                @endif
                                <div style="
                                    max-width:78%;
                                    padding:10px 16px;
                                    border-radius:{{ $isClient ? '18px 18px 4px 18px' : '18px 18px 18px 4px' }};
                                    background:{{ $isClient ? '#4b4ff5' : '#f0f2f8' }};
                                    color:{{ $isClient ? '#fff' : '#2c3150' }};
                                    font-size:14px;
                                    line-height:1.6;
                                    word-break:break-word;
                                ">
                                    {!! \App\Support\MessageFormatter::linkify(e($message->message), $isClient) !!}
                                </div>
                            </div>
                        @endif
                        @php $prevMessage = $message; @endphp
                    @empty
                        <div style="text-align:center;padding:40px 0">
                            <p style="color:var(--muted);font-size:15px">No messages yet. Start the conversation below.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Compose --}}
                <div style="padding:16px 28px 22px;border-top:1px solid var(--line);background:#fafbfe">
                    <form method="post" action="{{ route('workspace.messages.store') }}" style="display:flex;gap:10px;align-items:flex-end">
                        @csrf
                        <textarea class="textarea" id="message" name="message" rows="2" placeholder="Type a message..." required style="flex:1;resize:none;border-radius:16px;padding:12px 16px;font-size:14px;min-height:48px">{{ old('message') }}</textarea>
                        <button class="button button-primary" type="submit" style="border-radius:16px;padding:12px 22px;white-space:nowrap">Send</button>
                    </form>
                </div>
            @else
                <div style="padding:28px">
                    <div class="chat-card" style="background:#f8fbff;border-style:dashed">
                        <strong>Workspace note</strong>
                        <p class="muted" style="margin:8px 0 0">Create a project brief and send an offer before the message room becomes active.</p>
                    </div>
                    <div style="margin-top:18px">
                        <a class="button button-primary" href="{{ route('workspace.hire-flow') }}">Create project</a>
                    </div>
                </div>
            @endif
        </section>

        <aside class="sidebar-card">
            <h3 style="font-size:30px;letter-spacing:-.04em;margin:0 0 16px">Project snapshot</h3>
            @if ($project && $offer)
                <div class="setting-list">
                    <div class="setting-row">
                        <div>
                            <strong>{{ $project->title }}</strong>
                            <span>Current specialty: {{ $project->specialty }}</span>
                        </div>
                    </div>
                    <div class="setting-row">
                        <div>
                            <strong>Timeframe</strong>
                            <span>{{ $project->timeframe }}</span>
                        </div>
                    </div>
                    <div class="setting-row">
                        <div>
                            <strong>Rate</strong>
                            <span>${{ number_format((float) $offer->hourly_rate, 2) }} / hr</span>
                        </div>
                    </div>
                </div>

                <div class="inline-actions" style="margin-top:20px">
                    <a class="button button-primary" href="{{ $offer->status === 'active' ? route('workspace.project-active') : route('workspace.project-pending') }}">Open contract</a>
                    <a class="button button-secondary" href="{{ route('workspace.reports') }}">View reports</a>
                </div>
            @else
                <p class="empty">Project details appear here after you create your first offer.</p>
            @endif
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-scroll to bottom of chat
var chatScroll = document.getElementById('chat-scroll');
if (chatScroll) {
    chatScroll.scrollTop = chatScroll.scrollHeight;
}

// Auto-resize textarea
var textarea = document.getElementById('message');
if (textarea) {
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });
}
</script>
@endpush
