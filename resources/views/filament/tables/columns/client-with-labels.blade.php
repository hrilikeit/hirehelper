@php
    $record = $getRecord();
    $user = $record->user;
    $labels = $record->labels;
@endphp
<div>
    <div style="font-weight:600;font-size:14px;color:var(--fi-color-gray-950, #1f2937)">{{ $user?->name ?? '—' }}</div>
    <div style="font-size:12px;color:var(--fi-color-gray-500, #6b7280)">{{ $user?->email ?? '' }}</div>
    <div style="display:flex;flex-wrap:wrap;gap:3px;margin-top:4px;align-items:center">
        @foreach ($labels as $label)
            <span style="
                display:inline-flex;
                align-items:center;
                gap:3px;
                padding:1px 7px;
                border-radius:999px;
                font-size:10px;
                font-weight:600;
                color:#fff;
                background:{{ $label->color }};
                line-height:1.5;
            ">{{ $label->name }}</span>
        @endforeach
        <button
            type="button"
            wire:click="mountTableAction('manageLabels', '{{ $record->getKey() }}')"
            style="
                display:inline-flex;
                align-items:center;
                justify-content:center;
                width:18px;
                height:18px;
                border-radius:999px;
                border:1px dashed #d1d5db;
                background:none;
                color:#9ca3af;
                font-size:12px;
                cursor:pointer;
                line-height:1;
                padding:0;
            "
            title="Manage labels"
        >+</button>
    </div>
</div>
