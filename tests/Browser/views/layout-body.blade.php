<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    @metadata('meta', 'open-graph', 'twitter')
    @livewireStyles
</head>
<body>
    {{ $slot }}

    @metadata('json-ld')

    @livewireScripts
</body>
</html>
