@php
    $messages = $project->messages()->orderBy('sent_at')->get();
    $client = $project->user;
    $offer = $project->offers()->whereIn('status', ['active', 'pending', 'accepted'])->latest()->first()
        ?? $project->offers()->latest()->first();
    $freelancerName = $offer?->freelancer_display_name ?: 'Freelancer';
@endphp

<div style="border:1px solid #e5e7eb;border-radius:8px;background:#f9fafb;padding:12px;margin-bottom:8px;">
    <div style="display:flex;gap:24px;flex-wrap:wrap;font-size:13px;color:#374151;">
        <div><strong>Client:</strong> {{ $client?->name ?? '—' }} ({{ $client?->email ?? '—' }})</div>
        <div><strong>Freelancer:</strong> {{ $freelancerName }}</div>
        <div><strong>Project:</strong> {{ $project->title ?? '—' }}</div>
    </div>
</div>

<div style="max-height:520px;overflow-y:auto;padding:12px;border:1px solid #e5e7eb;border-radius:8px;background:#ffffff;">
    @forelse($messages as $m)
        @php $isClient = $m->sender_type === 'client'; @endphp
        <div style="display:flex;justify-content:{{ $isClient ? 'flex-start' : 'flex-end' }};margin-bottom:12px;">
            <div style="max-width:75%;background:{{ $isClient ? '#f3f4f6' : '#dbeafe' }};color:#111827;padding:10px 14px;border-radius:12px;border:1px solid {{ $isClient ? '#e5e7eb' : '#bfdbfe' }};">
                <div style="font-size:12px;font-weight:600;color:{{ $isClient ? '#374151' : '#1d4ed8' }};margin-bottom:4px;">
                    {{ $isClient ? 'Client' : 'Freelancer' }} — {{ $m->sender_name }}
                </div>
                <div style="font-size:14px;line-height:1.5;white-space:pre-wrap;">{{ $m->message }}</div>
                <div style="font-size:11px;color:#6b7280;margin-top:6px;text-align:right;">
                    {{ optional($m->sent_at)->format('M j, Y g:i A') ?? '—' }}
                </div>
            </div>
        </div>
    @empty
        <div style="text-align:center;color:#9ca3af;padding:24px;">No messages yet.</div>
    @endforelse
</div>
