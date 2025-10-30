@if (session()->hasAny(['success', 'error', 'warning', 'info']))
    <script>
        @foreach (['success', 'error', 'warning', 'info'] as $type)
            @if (session($type))
                showToast("{{ session($type) }}", "{{ $type }}");
            @endif
        @endforeach
    </script>
@endif
