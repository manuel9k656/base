<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdministracionController extends Controller
{
    // Vista principal de usuarios
    public function usuarios()
    {
        return view('administracion.usuarios');
    }

    // DataTable de usuarios
    public function usuarios_datatable(Request $request){
        $data = [];
        $users = User::all();
        
        foreach ($users as $user) {
            $dropdownItems = '';
            $hasActions = false;

            // Botón Ver
            if (Auth::user()->can('ver-usuarios')) {
                $dropdownItems .= '<li>
                                    <a class="dropdown-item" href="javascript:void(0);" onClick="verUsuario(' . $user->id . ')">
                                        <i class="ti ti-eye me-2"></i> Ver usuario
                                    </a>
                                </li>';
                $hasActions = true;
            }

            // Botón Editar
            if (Auth::user()->can('editar-usuarios')) {
                $dropdownItems .= '<li>
                                    <a class="dropdown-item" href="javascript:void(0);" onClick="editarUsuario(' . $user->id . ')">
                                        <i class="ti ti-pencil me-2"></i> Editar usuario
                                    </a>
                                </li>';
                $hasActions = true;
            }

            // Botón Roles
            if (Auth::user()->can('asignar-roles')) {
                $dropdownItems .= '<li>
                                    <a class="dropdown-item" href="javascript:void(0);" onClick="abrirModalRolesUsuario(' . $user->id . ', \'' . e($user->name) . '\')">
                                        <i class="ti ti-shield-check me-2"></i> Asignar roles
                                    </a>
                                </li>';
                $hasActions = true;
            }

            // Botón Cambiar Password
            if (Auth::user()->can('editar-usuarios')) {
                $dropdownItems .= '<li>
                                    <a class="dropdown-item" href="javascript:void(0);" onClick="cambiarPassword(' . $user->id . ', \'' . e($user->name) . '\')">
                                        <i class="ti ti-lock me-2"></i> Cambiar contraseña
                                    </a>
                                </li>';
                $hasActions = true;
            }

            // Separador antes de eliminar (si hay acciones previas)
            if (Auth::user()->can('eliminar-usuarios') && $user->id !== Auth::id() && $hasActions) {
                $dropdownItems .= '<li><hr class="dropdown-divider"></li>';
            }

            // Botón Eliminar
            if (Auth::user()->can('eliminar-usuarios') && $user->id !== Auth::id()) {
                $dropdownItems .= '<li>
                                    <a class="dropdown-item text-danger" href="javascript:void(0);" onClick="eliminarUsuario(' . $user->id . ')">
                                        <i class="ti ti-trash me-2"></i> Eliminar usuario
                                    </a>
                                </li>';
                $hasActions = true;
            }

            // Generar el dropdown completo o mensaje si es el usuario actual
           if ($hasActions) {
                $actionButtons = '
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i> Acciones
                        </button>
                        <ul class="dropdown-menu">
                            ' . $dropdownItems . '
                        </ul>
                    </div>';
            } else {
                $actionButtons = '<span class="badge bg-secondary">Sin permisos</span>';
            }

            // Email status
            $emailStatus = $user->email_verified_at 
                ? '<span class="badge bg-success"><i class="ti ti-check"></i> Verificado</span>'
                : '<span class="badge bg-warning"><i class="ti ti-clock"></i> Pendiente</span>';

            // Roles del usuario
            $rolesHtml = '';
            foreach ($user->roles as $role) {
                $rolesHtml .= '<span class="badge bg-primary me-1">' . $role->name . '</span>';
            }
            if (empty($rolesHtml)) {
                $rolesHtml = '<span class="badge bg-secondary">Sin rol</span>';
            }

            $data[] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $rolesHtml,
                'email_verified_at' => $emailStatus,
                'created_at' => $user->created_at ? $user->created_at->format('d/m/Y H:i') : '',
                'action' => $actionButtons
            ];
        }

        return response()->json(['data' => $data]);
    }

    // Modal para nuevo usuario
    public function nuevoUsuarioModal(Request $request)
    {
        $userId = $request->input('id');
        $roles = Role::all();

        if ($userId) {
            $user = User::with('roles')->findOrFail($userId);
            $titulo = 'Editar Usuario';
        } else {
            $user = null;
            $titulo = 'Nuevo Usuario';
        }

        return view('administracion.modals.usuario_form', compact('user', 'roles', 'titulo'));
    }

    // Crear usuario
    public function crearUsuario(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->id,
            'password' => $request->id ? 'nullable|min:8' : 'required|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if ($request->id) {
                // Actualizar
                $user = User::findOrFail($request->id);
                $user->name = $request->name;
                $user->email = $request->email;
                
                if ($request->filled('password')) {
                    $user->password = Hash::make($request->password);
                }
                
                $user->save();
                $mensaje = 'Usuario actualizado correctamente';
            } else {
                // Crear
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'email_verified_at' => now(),
                ]);
                $mensaje = 'Usuario creado correctamente';
            }

            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    // Modal de roles del usuario
    public function RolesUsuarioModal(Request $request)
    {
        $userId = $request->input('id');
        $user = User::with('roles')->findOrFail($userId);
        $roles = Role::all();
        $userRoles = $user->roles->pluck('id')->toArray();

        return view('administracion.modals.roles_usuario', compact('user', 'roles', 'userRoles'));
    }

    // Asignar roles al usuario
    public function asignarRoles(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $roles = $request->input('roles', []);

        try {
            $user->syncRoles($roles);

            return response()->json([
                'success' => true,
                'message' => 'Roles actualizados correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar roles: ' . $e->getMessage()
            ], 500);
        }
    }

    // Cambiar password
    public function cambiarPassword(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::findOrFail($id);
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Contraseña actualizada correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar la contraseña: ' . $e->getMessage()
            ], 500);
        }
    }

    // Eliminar usuario
    public function eliminarUsuario($id)
    {
        if ($id == Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes eliminar tu propio usuario'
            ], 403);
        }

        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el usuario: ' . $e->getMessage()
            ], 500);
        }
    }


     public function roles()
    {
        $roles = Role::with(['permissions', 'users'])->get();
        return view('administracion.roles', compact('roles'));
    }

    // Modal para crear/editar rol
    public function nuevoRolModal(Request $request)
    {
        $rolId = $request->input('id');
        $permissions = Permission::all()->groupBy(function($permission) {
            return explode('-', $permission->name)[1] ?? 'otros';
        });

        if ($rolId) {
            $rol = Role::with('permissions')->findOrFail($rolId);
            $titulo = 'Editar Rol';
        } else {
            $rol = null;
            $titulo = 'Nuevo Rol';
        }

        return view('administracion.modals.rol_form', compact('rol', 'permissions', 'titulo'));
    }

    // Crear o actualizar rol
    public function crearRol(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name,' . $request->id,
            'permissions' => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if ($request->id) {
                // Actualizar
                $rol = Role::findOrFail($request->id);
                $rol->name = $request->name;
                $rol->save();
                $mensaje = 'Rol actualizado correctamente';
            } else {
                // Crear
                $rol = Role::create(['name' => $request->name]);
                $mensaje = 'Rol creado correctamente';
            }

            // Sincronizar permisos
            $rol->syncPermissions($request->input('permissions', []));

            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'rol' => $rol
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el rol: ' . $e->getMessage()
            ], 500);
        }
    }

    // Eliminar rol
    public function eliminarRol($id)
    {
        try {
            $rol = Role::findOrFail($id);
            
            // No permitir eliminar roles del sistema
            if (in_array($rol->name, ['Administrador', 'Usuario', 'Supervisor'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar un rol del sistema'
                ], 403);
            }

            $rol->delete();

            return response()->json([
                'success' => true,
                'message' => 'Rol eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el rol: ' . $e->getMessage()
            ], 500);
        }
    }

    // Modal de usuarios por rol
    public function usuariosRolModal(Request $request)
    {
        $rolId = $request->input('id');
        $rol = Role::with('users')->findOrFail($rolId);

        return view('administracion.modals.usuarios_rol', compact('rol'));
    }
}