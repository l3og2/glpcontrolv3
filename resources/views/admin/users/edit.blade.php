@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h3>Editar Usuario: {{ $user->name }}</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT') 
           
            @if ($errors->any())
            
            Por favor, corrige los siguientes errores:

                        @foreach ($errors->all() as $error)

                {{ $error }}

                        @endforeach

            @endif
            
            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            
            
            <div class="mb-3">
                <label class="form-label">Roles</label>
                @foreach ($roles as $role)
                    <div class="form-check">
                        <input class="form-check-input" 
                               type="checkbox" 
                               name="roles[]" 
                               value="{{ $role->name }}"
                               @if($user->hasRole($role->name)) checked @endif>
                        <label class="form-check-label">{{ $role->name }}</label>
                    </div>
                @endforeach
            </div>
            
            <button type="submit" class="btn btn-corporate">Actualizar Usuario</button>
        </form>
    </div>
</div>
@endsection
