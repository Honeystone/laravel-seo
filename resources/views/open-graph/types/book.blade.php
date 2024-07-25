@if($type->author && !is_array($type->author))
    @include('honeystone-seo::open-graph.types.profile', ['type' => $type->author, 'alias' => 'book:author'])
@endif
@if(is_array($type->author) && count($type->author))
@foreach($type->author as $profile)
    @include('honeystone-seo::open-graph.types.profile', ['type' => $profile, 'alias' => 'book:author'])
@endforeach
@endif
@if($type->isbn)
    <meta property="book:isnb" content="{{ $type->isbn }}">
@endif
@if($type->releaseDate)
    <meta property="book:release_date" content="{{ $type->releaseDate->format('c') }}">
@endif
@if($type->tag && is_string($type->tag))
    <meta property="book:tag" content="{{ $type->tag }}">
@endif
@if(is_array($type->tag) && count($type->tag))
@foreach($type->tag as $tag)
    <meta property="book:tag" content="{{ $tag }}">
@endforeach
@endif
