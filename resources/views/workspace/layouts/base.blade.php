<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? 'Client workspace · HireHelper.ai' }}</title>
    <meta name="description" content="{{ $description ?? 'Client workspace for HireHelper.ai' }}" />
    <link rel="stylesheet" href="{{ asset('workspace-assets/css/styles.css') }}" />
</head>
<body>
<div class="app-shell">
    @include('workspace.partials.topbar', ['activeNav' => $activeNav ?? null])

    <main class="page-main">
        @yield('content')
    </main>

    @include('workspace.partials.footer')
</div>

<script src="{{ asset('workspace-assets/js/app.js') }}"></script>
@stack('scripts')
@include('partials.intercom')
</body>
</html>
