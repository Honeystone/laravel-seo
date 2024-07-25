@if(is_array($value) && count($value))
@foreach($value as $content)
@if($content)
    <meta name="{{ $name }}" content="{{ $content }}">
@endif
@endforeach
@endif
@if(is_string($value))
    <meta name="{{ $name }}" content="{{ $value }}">
@endif
