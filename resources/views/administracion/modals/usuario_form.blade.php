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
        <div class="col-md-12 mb-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title">Información adicional</h6>
                    <ul class="list-unstyled mb-0">
                        <li><strong>ID:</strong> {{ $user->id }}</li>
                        <li><strong>Fecha de registro:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</li>
                        <li><strong>Estado:</strong> 
                            @if($user->email_verified_at)
                                <span class="badge bg-success">Verificado</span>
                            @else
                                <span class="badge bg-warning">Pendiente de verificación</span>
                            @endif
                        </li>
                        <li><strong>Roles asignados:</strong>
                            @forelse($user->roles as $role)
                                <span class="badge bg-primary">{{ $role->name }}</span>
                            @empty
                                <span class="badge bg-secondary">Sin roles asignados</span>
                            @endforelse
                        </li>
                    </ul>
                </div>
            </div>
        </div>
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