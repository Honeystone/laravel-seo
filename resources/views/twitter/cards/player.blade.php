@if($card->player)
    <meta name="twitter:player" content="{{ $card->player }}">
@endif
@if($card->width)
    <meta name="twitter:player:width" content="{{ $card->width }}">
@endif
@if($card->height)
    <meta name="twitter:player:height" content="{{ $card->height }}">
@endif
@if($card->stream)
    <meta name="twitter:player:stream" content="{{ $card->stream }}">
@endif
