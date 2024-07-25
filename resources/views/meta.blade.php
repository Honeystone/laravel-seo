@props(array_merge([
    'title' => '',
    'titleTemplate' => '',
    'description' => '',
    'keywords' => [],
    'canonicalEnabled' => false,
    'canonical' => url()->current(),
    'robots' => [],
    'custom' => [],
], $config))

@if($title)
    <title>{{ str_replace('{title}', $title, $titleTemplate) }}</title>
@endif
@if($description)
    <meta name="description" content="{{ $description }}">
@endif
@if(count($keywords))
    <meta name="keywords" content="{{ implode(',', $keywords) }}">
@endif
@if($canonicalEnabled && $canonical)
    <link rel="canonical" href="{{ $canonical }}">
@endif
@if(count($robots))
    <meta name="robots" content="{{ implode(',', $robots) }}">
@endif
@if(count($custom))
@foreach($custom as $tag)
@foreach($tag as $name => $value)
    @include('honeystone-seo::meta.custom', compact('name', 'value'))
@endforeach
@endforeach
@endif
