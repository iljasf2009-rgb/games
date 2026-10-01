@extends('admin.layout')

@section('title', 'Permissies beheren')

@section('admin-content')
    <h2>Permissies beheren</h2>

    <form method="POST" action="{{ route('admin.permissions.store') }}" class="form-inline mb-4">
        @csrf
        <label for="new-permission" class="mr-2">Nieuwe permissie</label>
        <input id="new-permission" class="form-control mr-2" type="text" name="name" value="{{ old('name') }}" required maxlength="255">
        <button class="btn btn-success" type="submit">Toevoegen</button>
    </form>

    <table class="table table-striped">
        <thead><tr><th>Naam</th><th>Guard</th><th>Rollen</th><th>Acties</th></tr></thead>
        <tbody>
            @forelse ($permissions as $permission)
                <tr>
                    <td>
                        <form id="permission-{{ $permission->id }}" method="POST" action="{{ route('admin.permissions.update', $permission) }}">
                            @csrf
                            @method('PUT')
                            <input class="form-control" type="text" name="name" value="{{ $permission->name }}" required maxlength="255" aria-label="Naam van {{ $permission->name }}">
                        </form>
                    </td>
                    <td>{{ $permission->guard_name }}</td>
                    <td>{{ $permission->roles->pluck('name')->join(', ') ?: 'Niet gekoppeld' }}</td>
                    <td class="d-flex">
                        <button class="btn btn-primary btn-sm mr-2" type="submit" form="permission-{{ $permission->id }}">Opslaan</button>
                        <form method="POST" action="{{ route('admin.permissions.destroy', $permission) }}" onsubmit="return confirm('Deze permissie verwijderen?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" type="submit">Verwijderen</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">Er zijn nog geen permissies.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection