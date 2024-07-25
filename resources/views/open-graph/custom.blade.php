@if(is_array($value) && count($value))
@foreach($value as $content)
@if($content)
    <meta property="{{ $name }}" content="{{ $content }}">
@endif
@endforeach
@endif
@if(is_string($value))
    <meta property="{{ $name }}" content="{{ $value }}">
@endif
