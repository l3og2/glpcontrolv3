<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\State;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Empezamos con una consulta base de Eloquent, precargando las relaciones
        // para evitar el problema N+1 y hacer la consulta más eficiente.
        $query = User::with('state', 'roles');

        // Aplicamos el filtro si se envía un término de búsqueda
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            // Usamos un grupo de where para buscar en múltiples columnas
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('email', 'ILIKE', "%{$searchTerm}%");
            });
        }

        // Aplicamos el filtro si se selecciona un Estado
        if ($request->filled('state_id')) {
            $query->where('state_id', $request->state_id);
        }

        // Aplicamos el filtro si se selecciona un Rol
        if ($request->filled('role')) {
            // Usamos whereHas para filtrar basado en una relación
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Ejecutamos la consulta final, ordenamos por nombre, y paginamos.
        // withQueryString() es crucial para que los filtros se mantengan al cambiar de página.
        $users = $query->orderBy('name')->paginate(15)->withQueryString();
        
        // Obtenemos los datos para los menús desplegables del filtro
        $states = State::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        // Pasamos todas las variables necesarias a la vista
        return view('admin.users.index', compact('users', 'states', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $states = State::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        
        return view('admin.users.create', compact('states', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'state_id' => 'nullable|exists:states,id',
            'roles' => 'required|array'
        ]);
   
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'state_id' => $request->state_id,
            'password' => Hash::make($request->password), 
        ]);

        $user->assignRole($request->roles);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // Se puede añadir una capa extra de seguridad con Policies, pero esta verificación es funcional.
        if (Auth::user()->id === $user->id && Auth::user()->hasRole('Admin')) {
            return redirect()->route('admin.users.index')
                ->with('warning', 'Los administradores no pueden editar su propia cuenta desde este panel. Utilice la página de perfil.');
        }

        $states = State::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'states', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'state_id' => 'nullable|exists:states,id',
            'roles' => 'required|array',
            'password' => 'nullable|string|min:8',
        ]);

        $data = $request->only('name', 'email', 'state_id');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
           
        $user->update($data);
        $user->syncRoles($request->roles);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }       
        
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (Auth::user()->id === $user->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'No puedes eliminar tu propia cuenta de administrador.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }
    
    // El método show no se usa en un CRUD de este tipo, así que lo dejamos vacío o lo eliminamos.
    public function show(User $user)
    {
        //
    }
}