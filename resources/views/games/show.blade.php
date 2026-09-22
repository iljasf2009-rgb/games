@extends('base')

<a href="{{ route('games.index') }}">Terug naar overzicht</a>

@section('content')
<div class="container">
    <h1>{{ $game->name }}</h1>

    <ul>
        <li><strong>Platform:</strong> {{ $game->platform }}</li>
        <li><strong>Genre:</strong> {{ $game->genre }}</li>
        <li><strong>Rating:</strong> {{ $game->rating }}</li>
    </ul>

<a href="{{ route('games.index') }}">Terug naar overzicht</a></div>
@endsection