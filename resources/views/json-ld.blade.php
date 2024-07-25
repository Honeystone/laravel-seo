@props(array_merge([
    'enabled' => false,
    'generated' => '',
    'nonce' => '',
], $config))

@if($enabled)
    <!-- JSON-LD -->
    <script type="application/ld+json"@if($nonce) nonce="{{ $nonce }}"@endif>
        {!! $generated !!}
    </script>
@endif

