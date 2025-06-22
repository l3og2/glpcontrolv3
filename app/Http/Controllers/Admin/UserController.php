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
    public function index()
    {
        // 1. Obtenemos TODOS los usuarios de la base de datos.
        $users = User::all();

        // 2. Devolvemos la vista y le PASAMOS la variable $users.
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // 1. Obtenemos todos los estados de la base de datos.
        $states = State::all();

        // 2. Obtenemos todos los roles de la base de datos.
        $roles = Role::all();

        // 3. Devolvemos la vista y le PASAMOS AMBAS variables.
        return view('admin.users.create', compact('states', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    // --- 1. VALIDACIÓN ---
    // Este es el primer guardián. Si algo falla aquí, Laravel
    // automáticamente redirige al usuario al formulario con los errores.
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users', // 'unique:users' es crucial
        'password' => 'required|string|min:8',
        'state_id' => 'nullable|exists:states,id', // Permite que sea nulo, pero si se envía, debe existir en la tabla 'states'
        'roles' => 'required|array'
    ]);
   
    // --- 2. CREACIÓN DEL USUARIO ---
    // Usamos User::create para la asignación masiva.
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'state_id' => $request->state_id,
        // ¡MUY IMPORTANTE! Nunca guardes la contraseña en texto plano.
        'password' => Hash::make($request->password), 
    ]);

    // --- 3. ASIGNACIÓN DE ROLES ---
    // El paquete Spatie se encarga de esto elegantemente.
    $user->assignRole($request->roles);

    // --- 4. REDIRECCIÓN CON MENSAJE DE ÉXITO ---
    // Si el código llega hasta aquí, todo salió bien.
    return redirect()->route('admin.users.index')
        ->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // Verificación de seguridad: No permitir que el admin edite su propia cuenta
        if (Auth::user()->id == $user->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'No puedes editar tu propia cuenta.');
        }

        // Obtenemos todos los estados y roles para pasarlos a la vista
    
        $states = State::all();
        $roles = Role::all();

        // Pasamos el usuario específico que se va a editar, más las listas de estados y roles
        return view('admin.users.edit', compact('user', 'states', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // --- 1. VALIDACIÓN ---
        $request->validate([
        'name' => 'required|string|max:255',
        // 'unique' tiene una regla especial para 'update': debe ignorar el email del propio usuario
        'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        'state_id' => 'nullable|exists:states,id',
        'roles' => 'required|array',
        // La contraseña es opcional, solo la actualizamos si se envía una nueva
        'password' => 'nullable|string|min:8',
        ]);

        // --- 2. ACTUALIZACIÓN DEL USUARIO ---
        // Usamos un array para los datos a actualizar
        $data = $request->only('name', 'email', 'state_id');

        // Solo añadimos la contraseña al array si el campo no vino vacío
        if ($request->filled('password')) {
        $data['password'] = Hash::make($request->password);
        }
           
        $user->update($data);

        // --- 3. SINCRONIZACIÓN DE ROLES ---
        // 'syncRoles' es perfecto para 'update': quita los roles viejos y pone los nuevos.
        $user->syncRoles($request->roles);

        // --- 4. REDIRECCIÓN CON MENSAJE DE ÉXITO ---
        return redirect()->route('admin.users.index')
        ->with('success', 'Usuario actualizado exitosamente.');
    }       
        
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Verificación de seguridad: No permitir que el admin se elimine a sí mismo
        if (Auth::user()->id == $user->id) {
        return redirect()->route('admin.users.index')
        ->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
        ->with('success', 'Usuario eliminado exitosamente.');
    }
}