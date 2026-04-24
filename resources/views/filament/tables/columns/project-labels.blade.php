<div style="display:flex;flex-wrap:wrap;gap:4px">
    @foreach ($getRecord()->labels as $label)
        <span style="
            display:inline-block;
            padding:2px 8px;
            border-radius:999px;
            font-size:11px;
            font-weight:600;
            color:#fff;
            background:{{ $label->color }};
            line-height:1.4;
        ">{{ $label->name }}</span>
    @endforeach
</div>
