@extends('base')

@section('content')
    <nav class="nav nav-pills mb-4" aria-label="Beheeronderdelen">
        <a class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}" href="{{ route('admin.permissions.index') }}">Permissies</a>
        <a class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">Rollen</a>
        <a class="nav-link {{ request()->routeIs('admin.role-permissions.*') ? 'active' : '' }}" href="{{ route('admin.role-permissions.index') }}">Permissies aan rollen</a>
        <a class="nav-link {{ request()->routeIs('admin.user-roles.*') ? 'active' : '' }}" href="{{ route('admin.user-roles.index') }}">Rollen aan gebruikers</a>
    </nav>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('admin-content')
@endsection