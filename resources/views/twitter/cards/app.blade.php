@if($card->iphoneId)
    <meta name="twitter:app:id:iphone" content="{{ $card->iphoneId }}">
@endif
@if($card->iphoneName)
    <meta name="twitter:app:name:iphone" content="{{ $card->iphoneName }}">
@endif
@if($card->iphoneUrl)
    <meta name="twitter:app:url:iphone" content="{{ $card->iphoneUrl }}">
@endif
@if($card->ipadId)
    <meta name="twitter:app:id:ipad" content="{{ $card->ipadId }}">
@endif
@if($card->ipadName)
    <meta name="twitter:app:name:ipad" content="{{ $card->ipadName }}">
@endif
@if($card->ipadUrl)
    <meta name="twitter:app:url:ipad" content="{{ $card->ipadUrl }}">
@endif
@if($card->googlePlayId)
    <meta name="twitter:app:id:googleplay" content="{{ $card->googlePlayId }}">
@endif
@if($card->googlePlayName)
    <meta name="twitter:app:name:googleplay" content="{{ $card->googlePlayName }}">
@endif
@if($card->googlePlayUrl)
    <meta name="twitter:app:url:googleplay" content="{{ $card->googlePlayUrl }}">
@endif
