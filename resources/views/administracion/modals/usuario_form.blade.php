{{-- resources/views/administracion/modals/usuario_form.blade.php --}}
<form id="formUsuario" onsubmit="event.preventDefault(); guardarUsuario();">
    <input type="hidden" id="usuario_id" value="{{ $user->id ?? '' }}">
    
    <div class="row">
        <div class="col-md-12 mb-3">
            <label for="name" class="form-label">Nombre completo <span class="text-danger">*</span></label>
            <input type="text" 
                   class="form-control" 
                   id="name" 
                   name="name" 
                   value="{{ $user->name ?? '' }}" 
                   required>
        </div>

        <div class="col-md-12 mb-3">
            <label for="email" class="form-label">Correo electrónico <span class="text-danger">*</span></label>
            <input type="email"
                   class="form-control"
                   id="email"
                   name="email"
                   value="{{ $user->email ?? '' }}"
                   required>
        </div>

        @if(!$user)
        {{-- Contraseña requerida solo al crear --}}
        <div class="col-md-12 mb-3">
            <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
            <input type="password"
                   class="form-control"
                   id="password"
                   name="password"
                   placeholder="Mínimo 8 caracteres"
                   minlength="8"
                   required>
            <small class="text-muted">Mínimo 8 caracteres</small>
        </div>

        <div class="col-md-12 mb-3">
            <label for="password_confirmation" class="form-label">Confirmar contraseña <span class="text-danger">*</span></label>
            <input type="password"
                   class="form-control"
                   id="password_confirmation"
                   name="password_confirmation"
                   placeholder="Repite la contraseña"
                   minlength="8"
                   required>
        </div>
        @else
        {{-- Opcional al editar, usa el botón "Cambiar contraseña" separado --}}
        <div class="col-md-12 mb-3">
            <div class="alert alert-warning">
                <i class="ti ti-lock me-2"></i>
                <small><strong>Contraseña:</strong> Para cambiar la contraseña usa el botón "Cambiar contraseña" en la tabla de usuarios.</small>
            </div>
        </div>
        @endif

        @if(!$user)
        <div class="col-md-12 mb-3">
            <div class="alert alert-info">
                <i class="ti ti-info-circle me-2"></i>
                <small><strong>Nota:</strong> Podrás asignar roles al usuario después de crearlo usando el botón "Asignar roles" en la tabla de usuarios.</small>
            </div>
        </div>
        @endif
        @if($user)
        {{-- Información Básica --}}
        <div class="col-md-12 mb-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title mb-3">
                        <i class="ti ti-info-circle"></i> Información adicional
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><strong>ID:</strong> {{ $user->id }}</li>
                                <li class="mb-2"><strong>Fecha de registro:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</li>
                                <li class="mb-2">
                                    <strong>Estado:</strong>
                                    @if($user->email_verified_at)
                                        <span class="badge bg-success"><i class="ti ti-check"></i> Verificado</span>
                                    @else
                                        <span class="badge bg-warning"><i class="ti ti-clock"></i> Pendiente</span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <strong>Total de roles:</strong>
                                    <span class="badge bg-primary">{{ $user->roles->count() }}</span>
                                </li>
                                <li class="mb-2">
                                    <strong>Permisos directos:</strong>
                                    <span class="badge bg-info">{{ $userDirectPermissions->count() }}</span>
                                </li>
                                <li class="mb-2">
                                    <strong>Permisos de roles:</strong>
                                    <span class="badge bg-success">{{ $rolePermissions->count() }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Roles Asignados --}}
        <div class="col-md-12 mb-3">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="ti ti-shield-check"></i> Roles Asignados ({{ $user->roles->count() }})
                    </h6>
                </div>
                <div class="card-body">
                    @forelse($user->roles as $role)
                        <div class="mb-3 p-3 border rounded">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="mb-0">
                                    <span class="badge bg-primary fs-6">{{ $role->name }}</span>
                                </h6>
                                <small class="text-muted">{{ $role->permissions->count() }} permisos</small>
                            </div>
                            @if($role->permissions->count() > 0)
                                <div class="mt-2">
                                    <small class="text-muted d-block mb-1"><strong>Permisos del rol:</strong></small>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($role->permissions as $permission)
                                            <span class="badge bg-success-subtle text-success">
                                                <i class="ti ti-check"></i> {{ $permission->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <small class="text-muted"><em>Este rol no tiene permisos asignados</em></small>
                            @endif
                        </div>
                    @empty
                        <div class="alert alert-secondary mb-0">
                            <i class="ti ti-info-circle me-2"></i>
                            <strong>Sin roles asignados.</strong> Usa el botón "Asignar roles" en la tabla de usuarios.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Permisos Individuales --}}
        <div class="col-md-12 mb-3">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="ti ti-key"></i> Permisos Individuales ({{ $userDirectPermissions->count() }})
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        <small>
                            <strong>Permisos individuales:</strong> Son permisos específicos asignados directamente a este usuario,
                            además de los que hereda de sus roles.
                        </small>
                    </div>

                    @if($userDirectPermissions->count() > 0)
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @foreach($userDirectPermissions as $permission)
                                <span class="badge bg-info fs-6">
                                    <i class="ti ti-key"></i> {{ $permission->name }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-secondary mb-0">
                            <i class="ti ti-info-circle me-2"></i>
                            <strong>Sin permisos individuales.</strong> Usa el botón "Asignar permisos" en la tabla de usuarios.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Resumen de Permisos Totales --}}
        @php
            $allPermissions = $rolePermissions->merge($userDirectPermissions)->unique('id');
        @endphp
        @if($allPermissions->count() > 0)
        <div class="col-md-12 mb-3">
            <div class="card border-dark">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0">
                        <i class="ti ti-list-check"></i> Todos los Permisos ({{ $allPermissions->count() }} total)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-secondary mb-3">
                        <small>
                            <i class="ti ti-info-circle me-1"></i>
                            <strong>Leyenda:</strong>
                            <span class="badge bg-success ms-2">Verde</span> = Heredado de rol |
                            <span class="badge bg-info ms-2">Azul</span> = Permiso individual
                        </small>
                    </div>

                    <div class="row">
                        @php
                            $permissionsByCategory = $allPermissions->groupBy(function($permission) {
                                $parts = explode('-', $permission->name);
                                return count($parts) > 1 ? $parts[1] : 'otros';
                            });
                        @endphp

                        @foreach($permissionsByCategory as $category => $perms)
                            <div class="col-md-6 mb-3">
                                <div class="card bg-light h-100">
                                    <div class="card-body">
                                        <h6 class="card-title mb-2">
                                            <i class="ti ti-folder"></i> {{ ucfirst($category) }}
                                            <span class="badge bg-secondary">{{ $perms->count() }}</span>
                                        </h6>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($perms as $perm)
                                                @php
                                                    $isDirect = $userDirectPermissions->contains('id', $perm->id);
                                                    $isFromRole = $rolePermissions->contains('id', $perm->id);
                                                @endphp
                                                @if($isDirect && $isFromRole)
                                                    {{-- Tiene ambos --}}
                                                    <span class="badge bg-warning text-dark" title="Heredado de rol Y asignado individualmente">
                                                        <i class="ti ti-star"></i> {{ $perm->name }}
                                                    </span>
                                                @elseif($isDirect)
                                                    {{-- Solo individual --}}
                                                    <span class="badge bg-info">
                                                        <i class="ti ti-key"></i> {{ $perm->name }}
                                                    </span>
                                                @else
                                                    {{-- Solo de rol --}}
                                                    <span class="badge bg-success">
                                                        <i class="ti ti-shield-check"></i> {{ $perm->name }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endif
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
            <i class="ti ti-x"></i> Cerrar
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="ti ti-device-floppy"></i> {{ $user ? 'Actualizar' : 'Crear' }} Usuario
        </button>
    </div>
</form>

<script>
    // Actualizar título del modal
    $('#tituloUsuarioModal').text('{{ $titulo }}');
</script>