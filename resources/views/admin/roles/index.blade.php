@extends('admin.layout')

@section('title', 'Rollen beheren')

@section('admin-content')
    <h2>Rollen beheren</h2>

    <form method="POST" action="{{ route('admin.roles.store') }}" class="form-inline mb-4">
        @csrf
        <label for="new-role" class="mr-2">Nieuwe rol</label>
        <input id="new-role" class="form-control mr-2" type="text" name="name" value="{{ old('name') }}" required maxlength="255">
        <button class="btn btn-success" type="submit">Toevoegen</button>
    </form>

    <table class="table table-striped">
        <thead><tr><th>Naam</th><th>Guard</th><th>Gebruikers</th><th>Permissies</th><th>Acties</th></tr></thead>
        <tbody>
            @forelse ($roles as $role)
                <tr>
                    <td>
                        <form id="role-{{ $role->id }}" method="POST" action="{{ route('admin.roles.update', $role) }}">
                            @csrf
                            @method('PUT')
                            <input class="form-control" type="text" name="name" value="{{ $role->name }}" required maxlength="255" aria-label="Naam van {{ $role->name }}">
                        </form>
                    </td>
                    <td>{{ $role->guard_name }}</td>
                    <td>{{ $role->users->pluck('name')->join(', ') ?: 'Geen' }}</td>
                    <td>{{ $role->permissions->pluck('name')->join(', ') ?: 'Geen' }}</td>
                    <td class="d-flex">
                        <button class="btn btn-primary btn-sm mr-2" type="submit" form="role-{{ $role->id }}">Opslaan</button>
                        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('Deze rol verwijderen?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" type="submit">Verwijderen</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">Er zijn nog geen rollen.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection