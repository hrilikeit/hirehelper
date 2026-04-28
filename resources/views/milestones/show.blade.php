@extends('workspace.layouts.base', ['activeNav' => 'milestones', 'title' => $project->title . ' — Milestones'])

@section('content')
<div class="container" style="max-width:860px;margin:0 auto;padding:40px 20px">
    @include('workspace.partials.flash')

    {{-- Header --}}
    <div style="margin-bottom:32px">
        <h1 style="font-size:28px;font-weight:700;letter-spacing:-.03em;margin:0 0 8px">{{ $project->title }}</h1>
        @if ($project->description)
            <p style="color:#6b7280;font-size:15px;margin:0 0 12px;line-height:1.6">{{ $project->description }}</p>
        @endif
        <div style="display:flex;gap:16px;align-items:center;flex-wrap:wrap">
            @if ($freelancer)
                <span style="font-size:14px;color:#374151">
                    <strong>Freelancer:</strong> {{ $freelancer->name }}
                </span>
            @endif
            <span style="font-size:14px;padding:3px 10px;border-radius:999px;font-weight:600;
                background:{{ $project->status === 'active' ? '#dcfce7' : ($project->status === 'completed' ? '#dbeafe' : '#f3f4f6') }};
                color:{{ $project->status === 'active' ? '#166534' : ($project->status === 'completed' ? '#1e40af' : '#6b7280') }}">
                {{ ucfirst($project->status) }}
            </span>
        </div>
    </div>

    {{-- Login/Register prompt if not logged in --}}
    @guest
        <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:16px;padding:20px 24px;margin-bottom:28px">
            <p style="margin:0 0 12px;font-size:15px;color:#92400e"><strong>Sign in to manage milestones</strong></p>
            <p style="margin:0 0 16px;font-size:14px;color:#a16207">Register or log in to fund milestones and track progress.</p>
            <div style="display:flex;gap:10px">
                <a href="{{ route('client.register') }}?redirect={{ urlencode('/m/' . $project->token) }}" class="button button-primary" style="font-size:14px;padding:10px 20px">Join as a client</a>
                <a href="{{ route('client.login') }}?redirect={{ urlencode('/m/' . $project->token) }}" class="button button-secondary" style="font-size:14px;padding:10px 20px">Log in</a>
            </div>
        </div>
    @endguest

    {{-- Payment method prompt --}}
    @auth
        @if ($isOwner && ! $hasBillingMethod)
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:16px;padding:20px 24px;margin-bottom:28px">
                <p style="margin:0 0 8px;font-size:15px;color:#991b1b"><strong>Payment method required</strong></p>
                <p style="margin:0 0 14px;font-size:14px;color:#b91c1c">Add a payment method to fund milestones.</p>
                <a href="{{ route('workspace.billing-method') }}" class="button button-primary" style="font-size:14px;padding:10px 20px">Add payment method</a>
            </div>
        @endif
    @endauth

    {{-- Summary bar --}}
    <div style="display:flex;gap:16px;margin-bottom:28px;flex-wrap:wrap">
        <div style="flex:1;min-width:140px;background:#f9fafb;border-radius:14px;padding:16px 20px">
            <div style="font-size:12px;color:#6b7280;margin-bottom:4px">Total project</div>
            <div style="font-size:22px;font-weight:700">${{ number_format((float) $project->total_amount, 2) }}</div>
        </div>
        <div style="flex:1;min-width:140px;background:#f0fdf4;border-radius:14px;padding:16px 20px">
            <div style="font-size:12px;color:#6b7280;margin-bottom:4px">Funded</div>
            <div style="font-size:22px;font-weight:700;color:#16a34a">${{ number_format($project->total_funded, 2) }}</div>
        </div>
        <div style="flex:1;min-width:140px;background:#eff6ff;border-radius:14px;padding:16px 20px">
            <div style="font-size:12px;color:#6b7280;margin-bottom:4px">Released</div>
            <div style="font-size:22px;font-weight:700;color:#2563eb">${{ number_format($project->total_released, 2) }}</div>
        </div>
    </div>

    {{-- Milestones list --}}
    <div style="display:flex;flex-direction:column;gap:16px">
        @foreach ($milestones as $index => $milestone)
            <div style="
                border:1px solid {{ $milestone->isReleased() ? '#bbf7d0' : ($milestone->isFunded() ? '#bfdbfe' : '#e5e7eb') }};
                border-radius:16px;
                padding:24px;
                background:{{ $milestone->isReleased() ? '#f0fdf4' : ($milestone->isFunded() ? '#eff6ff' : '#fff') }};
                transition:box-shadow .15s ease;
            ">
                {{-- Milestone header --}}
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:12px">
                    <div style="display:flex;align-items:center;gap:10px">
                        <span style="
                            display:inline-flex;align-items:center;justify-content:center;
                            width:30px;height:30px;border-radius:50%;font-size:13px;font-weight:700;
                            background:{{ $milestone->isReleased() ? '#16a34a' : ($milestone->isFunded() ? '#2563eb' : '#e5e7eb') }};
                            color:{{ $milestone->isPending() ? '#374151' : '#fff' }};
                        ">{{ $index + 1 }}</span>
                        <h3 style="font-size:17px;font-weight:600;margin:0">{{ $milestone->name }}</h3>
                    </div>
                    <div style="text-align:right">
                        <div style="font-size:18px;font-weight:700">${{ number_format((float) $milestone->amount, 2) }}</div>
                        <span style="font-size:11px;font-weight:600;padding:2px 8px;border-radius:999px;
                            background:{{ $milestone->isReleased() ? '#dcfce7' : ($milestone->isFunded() ? '#dbeafe' : '#f3f4f6') }};
                            color:{{ $milestone->isReleased() ? '#166534' : ($milestone->isFunded() ? '#1e40af' : '#6b7280') }};
                        ">{{ ucfirst($milestone->status) }}</span>
                    </div>
                </div>

                {{-- Description --}}
                @if ($milestone->description)
                    <p style="margin:0 0 16px;font-size:14px;color:#4b5563;line-height:1.6">{{ $milestone->description }}</p>
                @endif

                {{-- Timestamps --}}
                @if ($milestone->funded_at || $milestone->released_at)
                    <div style="font-size:12px;color:#9ca3af;margin-bottom:12px">
                        @if ($milestone->funded_at)
                            Funded {{ $milestone->funded_at->format('M j, Y g:i A') }}
                        @endif
                        @if ($milestone->released_at)
                            · Released {{ $milestone->released_at->format('M j, Y g:i A') }}
                        @endif
                    </div>
                @endif

                {{-- Actions --}}
                @auth
                    @if ($isOwner)
                        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
                            @if ($milestone->isPending())
                                {{-- Edit button --}}
                                <button type="button" onclick="toggleEdit({{ $milestone->id }})" class="button button-secondary" style="font-size:13px;padding:8px 16px">Edit</button>

                                {{-- Fund button --}}
                                @if ($hasBillingMethod)
                                    <form method="post" action="{{ route('milestones.fund', [$project->token, $milestone->id]) }}" style="margin:0">
                                        @csrf
                                        <button type="submit" class="button button-primary" style="font-size:13px;padding:8px 16px" onclick="return confirm('Fund {{ $milestone->name }} for ${{ number_format((float) $milestone->amount, 2) }}?')">
                                            Fund ${{ number_format((float) $milestone->amount, 2) }}
                                        </button>
                                    </form>
                                @endif
                            @elseif ($milestone->isFunded())
                                <form method="post" action="{{ route('milestones.release', [$project->token, $milestone->id]) }}" style="margin:0">
                                    @csrf
                                    <button type="submit" class="button button-primary" style="font-size:13px;padding:8px 16px;background:#16a34a" onclick="return confirm('Release funds for {{ $milestone->name }}?')">
                                        Release
                                    </button>
                                </form>
                                <span style="font-size:13px;color:#2563eb">Payment held — release when ready</span>
                            @elseif ($milestone->isReleased())
                                <span style="font-size:13px;color:#16a34a;font-weight:600">✓ Completed</span>
                            @endif
                        </div>

                        {{-- Edit form (hidden by default) --}}
                        @if ($milestone->isPending())
                            <div id="edit-{{ $milestone->id }}" style="display:none;margin-top:16px;padding-top:16px;border-top:1px solid #e5e7eb">
                                <form method="post" action="{{ route('milestones.update', [$project->token, $milestone->id]) }}">
                                    @csrf
                                    <div style="margin-bottom:10px">
                                        <label style="font-size:13px;font-weight:600;display:block;margin-bottom:4px">Name</label>
                                        <input class="input" name="name" value="{{ $milestone->name }}" required style="width:100%" />
                                    </div>
                                    <div style="margin-bottom:10px">
                                        <label style="font-size:13px;font-weight:600;display:block;margin-bottom:4px">Description</label>
                                        <textarea class="textarea" name="description" rows="3" style="width:100%">{{ $milestone->description }}</textarea>
                                    </div>
                                    <div style="display:flex;gap:8px">
                                        <button type="submit" class="button button-primary" style="font-size:13px;padding:8px 16px">Save</button>
                                        <button type="button" onclick="toggleEdit({{ $milestone->id }})" class="button button-secondary" style="font-size:13px;padding:8px 16px">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    @endif
                @endauth
            </div>
        @endforeach
    </div>

    @if ($milestones->isEmpty())
        <div style="text-align:center;padding:60px 20px;color:#9ca3af">
            <p style="font-size:16px">No milestones have been added yet.</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function toggleEdit(id) {
    var el = document.getElementById('edit-' + id);
    if (el) {
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }
}
</script>
@endpush
