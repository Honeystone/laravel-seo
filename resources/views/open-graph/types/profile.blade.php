@php
    $alias ??= 'profile';

    $map = [
        "$alias:first_name" => 'firstName',
        "$alias:last_name" => 'lastName',
        "$alias:username" => 'username',
        "$alias:gender" => 'gender',
    ];
@endphp

@foreach($map as $name => $property)
@if($type->$property)
    <meta property="{{ $name }}" content="{{ $type->$property }}">
@endif
@endforeach
