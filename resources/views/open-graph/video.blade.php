@php
    $map = [
        'og:video' => 'url',
        'og:video:alt' => 'alt',
        'og:video:width' => 'width',
        'og:video:height' => 'height',
        'og:video:secure_url' => 'secureUrl',
        'og:video:type' => 'type',
    ];
@endphp

@if($video instanceof \Honeystone\Seo\OpenGraph\VideoProperties)
@foreach($map as $name => $property)
@if($video->$property)
    <meta property="{{ $name }}" content="{{ $video->$property }}">
@endif
@endforeach
@else
    <meta property="og:video" content="{{ $video }}">
@endif
