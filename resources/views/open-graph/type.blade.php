@if($type instanceof \Honeystone\Seo\OpenGraph\Contracts\Type)
    <meta property="og:type" content="{{ $type->getType() }}">
@switch($type::class)
@case(\Honeystone\Seo\OpenGraph\ArticleProperties::class)
    @include('honeystone-seo::open-graph.types.article', compact('type'))
@break
@case(\Honeystone\Seo\OpenGraph\ProfileProperties::class)
    @include('honeystone-seo::open-graph.types.profile', compact('type'))
@break
@case(\Honeystone\Seo\OpenGraph\BookProperties::class)
    @include('honeystone-seo::open-graph.types.book', compact('type'))
@break
@endswitch
@else
    <meta property="og:type" content="{{ $type }}">
@endif
