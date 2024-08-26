@if($card instanceof \Honeystone\Seo\Twitter\Contracts\Card)
    <meta name="twitter:card" content="{{ $card->getName() }}">
@switch($card::class)
@case(\Honeystone\Seo\Twitter\AppProperties::class)
    @include('honeystone-seo::twitter.cards.app', compact('card'))
@break
@case(\Honeystone\Seo\Twitter\PlayerProperties::class)
    @include('honeystone-seo::twitter.cards.player', compact('card'))
@endswitch
@else
    <meta name="twitter:card" content="{{ $card }}">
@endif
