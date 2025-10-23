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

        <div class="col-md-12 mb-3">
            <label for="roles" class="form-label">Roles asignados</label>
            <select id="roles" 
                    name="roles[]" 
                    class="form-select" 
                    multiple>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}"
                        {{ isset($user) && $user->roles->contains($role->id) ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
        </div>
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