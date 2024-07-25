@php
    $map = [
        'og:image' => 'url',
        'og:image:alt' => 'alt',
        'og:image:width' => 'width',
        'og:image:height' => 'height',
        'og:image:secure_url' => 'secureUrl',
        'og:image:type' => 'type',
    ];
@endphp

@if($image instanceof \Honeystone\Seo\OpenGraph\ImageProperties)
@foreach($map as $name => $property)
@if($image->$property)
    <meta property="{{ $name }}" content="{{ $image->$property }}">
@endif
@endforeach
@else
    <meta property="og:image" content="{{ $image }}">
@endif
