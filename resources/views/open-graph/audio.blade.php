@php
    $map = [
        'og:audio' => 'url',
        'og:audio:secure_url' => 'secureUrl',
        'og:audio:type' => 'type',
    ];
@endphp

@if($audio instanceof \Honeystone\Seo\OpenGraph\AudioProperties)
@foreach($map as $name => $property)
@if($audio->$property)
    <meta property="{{ $name }}" content="{{ $audio->$property }}">
@endif
@endforeach
@else
    <meta property="og:audio" content="{{ $audio }}">
@endif
