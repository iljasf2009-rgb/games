@extends('admin.layout')

@section('title', 'Permissies aan rollen koppelen')

@section('admin-content')
    <h2>Permissies aan rollen koppelen</h2>

    <form method="POST" action="{{ route('admin.role-permissions.store') }}" class="form-inline mb-4">
        @csrf
        <label for="new-role-id" class="mr-2">Rol</label>
        <select id="new-role-id" class="form-control mr-2" name="role_id" required>
            @foreach ($roles as $role)
                <option value="{{ $role->id }}">{{ $role->name }}</option>
            @endforeach
        </select>
        <label for="new-permission-id" class="mr-2">Permissie</label>
        <select id="new-permission-id" class="form-control mr-2" name="permission_id" required>
            @foreach ($permissions as $permission)
                <option value="{{ $permission->id }}">{{ $permission->name }}</option>
            @endforeach
        </select>
        <button class="btn btn-success" type="submit" @disabled($roles->isEmpty() || $permissions->isEmpty())>Koppelen</button>
    </form>

    <table class="table table-striped">
        <thead><tr><th>Rol</th><th>Permissie</th><th>Acties</th></tr></thead>
        <tbody>
            @php($assignmentCount = 0)
            @foreach ($roles as $role)
                @foreach ($role->permissions as $permission)
                    @php($assignmentCount++)
                    <tr>
                        <td>
                            <form id="role-permission-{{ $role->id }}-{{ $permission->id }}" method="POST" action="{{ route('admin.role-permissions.update', [$role, $permission]) }}">
                                @csrf
                                @method('PUT')
                                <select class="form-control" name="role_id" aria-label="Rol">
                                    @foreach ($roles as $option)
                                        <option value="{{ $option->id }}" @selected($option->id === $role->id)>{{ $option->name }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td>
                            <select class="form-control" name="permission_id" form="role-permission-{{ $role->id }}-{{ $permission->id }}" aria-label="Permissie">
                                @foreach ($permissions as $option)
                                    <option value="{{ $option->id }}" @selected($option->id === $permission->id)>{{ $option->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="d-flex">
                            <button class="btn btn-primary btn-sm mr-2" type="submit" form="role-permission-{{ $role->id }}-{{ $permission->id }}">Opslaan</button>
                            <form method="POST" action="{{ route('admin.role-permissions.destroy', [$role, $permission]) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" type="submit">Verwijderen</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @endforeach
            @if ($assignmentCount === 0)
                <tr><td colspan="3">Er zijn nog geen permissies aan rollen gekoppeld.</td></tr>
            @endif
        </tbody>
    </table>
@endsection