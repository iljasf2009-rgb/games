@extends('admin.layout')

@section('title', 'Rollen aan gebruikers koppelen')

@section('admin-content')
    <h2>Rollen aan gebruikers koppelen</h2>

    <form method="POST" action="{{ route('admin.user-roles.store') }}" class="form-inline mb-4">
        @csrf
        <label for="new-user-id" class="mr-2">Gebruiker</label>
        <select id="new-user-id" class="form-control mr-2" name="user_id" required>
            @foreach ($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
            @endforeach
        </select>
        <label for="new-user-role-id" class="mr-2">Rol</label>
        <select id="new-user-role-id" class="form-control mr-2" name="role_id" required>
            @foreach ($roles as $role)
                <option value="{{ $role->id }}">{{ $role->name }}</option>
            @endforeach
        </select>
        <button class="btn btn-success" type="submit" @disabled($users->isEmpty() || $roles->isEmpty())>Koppelen</button>
    </form>

    <table class="table table-striped">
        <thead><tr><th>Gebruiker</th><th>Rol</th><th>Acties</th></tr></thead>
        <tbody>
            @php($assignmentCount = 0)
            @foreach ($users as $user)
                @foreach ($user->roles as $role)
                    @php($assignmentCount++)
                    <tr>
                        <td>
                            <form id="user-role-{{ $user->id }}-{{ $role->id }}" method="POST" action="{{ route('admin.user-roles.update', [$user, $role]) }}">
                                @csrf
                                @method('PUT')
                                <select class="form-control" name="user_id" aria-label="Gebruiker">
                                    @foreach ($users as $option)
                                        <option value="{{ $option->id }}" @selected($option->id === $user->id)>{{ $option->name }} ({{ $option->email }})</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td>
                            <select class="form-control" name="role_id" form="user-role-{{ $user->id }}-{{ $role->id }}" aria-label="Rol">
                                @foreach ($roles as $option)
                                    <option value="{{ $option->id }}" @selected($option->id === $role->id)>{{ $option->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="d-flex">
                            <button class="btn btn-primary btn-sm mr-2" type="submit" form="user-role-{{ $user->id }}-{{ $role->id }}">Opslaan</button>
                            <form method="POST" action="{{ route('admin.user-roles.destroy', [$user, $role]) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" type="submit">Verwijderen</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @endforeach
            @if ($assignmentCount === 0)
                <tr><td colspan="3">Er zijn nog geen rollen aan gebruikers gekoppeld.</td></tr>
            @endif
        </tbody>
    </table>
@endsection