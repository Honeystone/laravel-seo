@props(array_merge([
    'enabled' => false,
    'site' => '',
    'type' => '',
    'title' => '',
    'description' => '',
    'images' => [],
    'audio' => [],
    'videos' => [],
    'determiner' => '',
    'url' => url()->current(),
    'locale' => '',
    'alternateLocales' => [],
    'custom' => [],
], $config))

@if($enabled)
    <!-- Open Graph -->
@if($site)
    <meta property="og:site_name" content="{{ $site }}">
@endif
@if($type)
    @include('honeystone-seo::open-graph.type', compact('type'))
@endif
@if($title)
    <meta property="og:title" content="{{ $title }}">
@endif
@if($description)
    <meta property="og:description" content="{{ $description }}">
@endif
@if(count($images))
@foreach($images as $image)
    @include('honeystone-seo::open-graph.image', compact('image'))
@endforeach
@endif
@if($url)
    <meta property="og:url" content="{{ $url }}">
@endif
@if(count($audio))
@foreach($audio as $each)
    @include('honeystone-seo::open-graph.audio', ['audio' => $each])
@endforeach
@endif
@if(count($videos))
@foreach($videos as $video)
    @include('honeystone-seo::open-graph.video', compact('video'))
@endforeach
@endif
@if($determiner)
    <meta property="og:determiner" content="{{ $determiner }}">
@endif
@if($locale)
    <meta property="og:locale" content="{{ $locale }}">
@endif
@if(count($alternateLocales))
@foreach($alternateLocales as $locale)
    <meta property="og:locale:alternate" content="{{ $locale }}">
@endforeach
@endif
@endif
@if(count($custom))
@foreach($custom as $tag)
@foreach($tag as $name => $value)
    @include('honeystone-seo::open-graph.custom', compact('name', 'value'))
@endforeach
@endforeach
@endif
