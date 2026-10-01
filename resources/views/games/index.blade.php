@extends('base')

@section('title', 'Game Collection')

@section('content')
    @role('admin')
        <p><a href="{{ route('games.create') }}" class="btn btn-success">Add Game</a></p>
    @endrole

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Game</th>
                <th>Platform</th>
                <th>Genre</th>
                <th>Rating</th>
                @role('admin')
                    <th>Show</th>
                    <th>Edit</th>
                    <th>Delete</th>
                @endrole
            </tr>
        </thead>
        <tbody>
            @foreach($games as $game)
                <tr>
                    <td>{{ $game->id }}</td>
                    <td>{{ $game->game_name }}</td>
                    <td>{{ $game->platform }}</td>
                    <td>{{ $game->genre }}</td>
                    <td>{{ $game->rating }}/10</td>
                    @role('admin')
                        <td><a href="{{ route('games.show', $game->id) }}" class="btn btn-info btn-sm">Show</a></td>
                        <td><a href="{{ route('games.edit', $game->id) }}" class="btn btn-primary btn-sm">Edit</a></td>
                        <td>
                            <form action="{{ route('games.destroy', $game->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    @endrole
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">Average rating:</th>
                <th>{{ number_format($games->avg('rating') ?? 0, 1) }}/10</th>
                @role('admin')
                    <th colspan="3"></th>
                @endrole
            </tr>
        </tfoot>
    </table>
@endsection
