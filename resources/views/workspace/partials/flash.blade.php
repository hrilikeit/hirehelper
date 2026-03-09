@if (session('success') || session('info') || $errors->any())
    <div style="display:grid;gap:12px;margin-bottom:22px">
        @if (session('success'))
            <div class="notice-banner" style="border-color:#d5f5df;background:#f3fff7">
                <div style="display:flex;align-items:center;gap:12px">
                    <span class="notice-icon">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('info'))
            <div class="notice-banner" style="border-color:#dbe7ff;background:#f6f9ff">
                <div style="display:flex;align-items:center;gap:12px">
                    <span class="notice-icon">ℹ</span>
                    <span>{{ session('info') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="notice-banner" style="border-color:#ffd2d7;background:#fff7f8">
                <div>
                    <strong>Please fix the following:</strong>
                    <ul style="margin:10px 0 0 18px;padding:0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
@endif
