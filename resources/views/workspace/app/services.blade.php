@extends('workspace.layouts.base', ['activeNav' => 'services'])

@section('content')
<div class="container">
    @include('workspace.partials.flash')

    <div class="page-heading">
        <div>
            <h1>My Services</h1>
            <p>Manage your active service subscriptions and communicate with freelancers.</p>
        </div>
    </div>

    @if ($subscriptions->isEmpty())
        <div class="project-card" style="text-align:center;padding:60px 20px">
            <p class="muted">You have no active service subscriptions yet.</p>
        </div>
    @else
        <div style="display:flex;flex-direction:column;gap:20px">
            @foreach ($subscriptions as $sub)
                <div class="project-card">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:20px;flex-wrap:wrap">
                        <div style="display:flex;align-items:center;gap:16px">
                            @if ($sub->service?->freelancer)
                                <img src="{{ $sub->service->freelancer->avatar_url }}" alt="{{ $sub->service->freelancer->name }}" style="width:56px;height:56px;border-radius:50%;object-fit:cover">
                            @endif
                            <div>
                                <h3 style="margin:0;font-size:20px;letter-spacing:-.02em">{{ $sub->service?->name ?? 'Unknown service' }}</h3>
                                <div class="muted small">by {{ $sub->service?->freelancer?->name ?? '—' }}</div>
                            </div>
                        </div>
                        <div style="text-align:right">
                            <div style="font-size:24px;font-weight:700">${{ number_format((float) $sub->amount, 2) }}<span class="muted" style="font-size:14px;font-weight:400">/month</span></div>
                            <span class="badge badge-{{ $sub->status === 'active' ? 'success' : ($sub->status === 'paused' ? 'warning' : 'danger') }}">{{ ucfirst($sub->status) }}</span>
                        </div>
                    </div>

                    <div class="separator" style="margin:16px 0"></div>

                    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
                        <div class="muted small">Subscribed: {{ $sub->subscribed_at?->format('M j, Y') ?: '—' }}</div>
                        @if ($sub->next_billing_at)
                            <div class="muted small">Next billing: {{ $sub->next_billing_at->format('M j, Y') }}</div>
                        @endif
                    </div>

                    <div style="display:flex;align-items:center;gap:10px;margin-top:16px;flex-wrap:wrap">
                        <a class="button button-primary button-compact" href="{{ route('workspace.messages') }}">Chat with freelancer</a>

                        @if ($sub->status === 'active')
                            <form method="post" action="{{ route('workspace.service-subscription.pause', $sub->id) }}" style="display:inline">
                                @csrf
                                <button class="button button-secondary button-compact" type="submit">Pause</button>
                            </form>
                            <form method="post" action="{{ route('workspace.service-subscription.cancel', $sub->id) }}" style="display:inline" onsubmit="return confirm('Are you sure you want to cancel this subscription?')">
                                @csrf
                                <button class="button button-secondary button-compact" type="submit" style="color:var(--danger)">Cancel</button>
                            </form>
                        @elseif ($sub->status === 'paused')
                            <form method="post" action="{{ route('workspace.service-subscription.resume', $sub->id) }}" style="display:inline">
                                @csrf
                                <button class="button button-secondary button-compact" type="submit">Resume</button>
                            </form>
                            <form method="post" action="{{ route('workspace.service-subscription.cancel', $sub->id) }}" style="display:inline" onsubmit="return confirm('Are you sure you want to cancel this subscription?')">
                                @csrf
                                <button class="button button-secondary button-compact" type="submit" style="color:var(--danger)">Cancel</button>
                            </form>
                        @elseif ($sub->status === 'cancelled')
                            <span class="muted small">Cancelled on {{ $sub->cancelled_at?->format('M j, Y') }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
