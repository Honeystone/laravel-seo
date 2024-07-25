@props(array_merge([
    'enabled' => false,
    'site' => '',
    'creator' => '',
    'title' => '',
    'description' => '',
    'image' => '',
], $config))

@if($enabled)
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
@if($site)
    <meta name="twitter:site" content="{{ $site }}">
@endif
@if($creator)
    <meta name="twitter:creator" content="{{ $creator }}">
@endif
@if($title)
    <meta name="twitter:title" content="{{ $title }}">
@endif
@if($description)
    <meta name="twitter:description" content="{{ $description }}">
@endif
@if($image)
    <meta name="twitter:image" content="{{ $image }}">
@endif
@endif
