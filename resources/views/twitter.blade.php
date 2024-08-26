@props(array_merge([
    'enabled' => false,
    'site' => '',
    'card' => 'summary_large_image',
    'creator' => '',
    'creatorId' => '',
    'title' => '',
    'description' => '',
    'image' => '',
    'imageAlt' => '',
], $config))

@if($enabled)
    <!-- Twitter Cards -->
    @include('honeystone-seo::twitter.card', compact('card'))
@if($site)
    <meta name="twitter:site" content="{{ $site }}">
@endif
@if($creator)
    <meta name="twitter:creator" content="{{ $creator }}">
@endif
@if($creatorId)
    <meta name="twitter:creator:id" content="{{ $creatorId }}">
@endif
@if($title)
    <meta name="twitter:title" content="{{ $title }}">
@endif
@if($description)
    <meta name="twitter:description" content="{{ $description }}">
@endif
@if($image)
    <meta name="twitter:image" content="{{ $image }}">
@if($imageAlt)
    <meta name="twitter:image:alt" content="{{ $imageAlt }}">
@endif
@endif
@endif
