@if($type->publishedTime)
    <meta property="article:published_time" content="{{ $type->publishedTime->format('c') }}">
@endif
@if($type->modifiedTime)
    <meta property="article:modified_time" content="{{ $type->publishedTime->format('c') }}">
@endif
@if($type->expirationTime)
    <meta property="article:expiration_time" content="{{ $type->publishedTime->format('c') }}">
@endif
@if($type->author && !is_array($type->author))
    @include('honeystone-seo::open-graph.types.profile', ['type' => $type->author, 'alias' => 'article:author'])
@endif
@if(is_array($type->author) && count($type->author))
@foreach($type->author as $profile)
    @include('honeystone-seo::open-graph.types.profile', ['type' => $profile, 'alias' => 'article:author'])
@endforeach
@endif
@if($type->section)
    <meta property="article:section" content="{{ $type->section }}">
@endif
@if($type->tag && is_string($type->tag))
    <meta property="article:tag" content="{{ $type->tag }}">
@endif
@if(is_array($type->tag) && count($type->tag))
@foreach($type->tag as $tag)
    <meta property="article:tag" content="{{ $tag }}">
@endforeach
@endif
