{{-- resources/views/administracion/modals/rol_form.blade.php --}}
<form id="formRol" onsubmit="event.preventDefault(); guardarRol();">
    <input type="hidden" id="rol_id" value="{{ $rol->id ?? '' }}">
    
    <div class="row">
        <div class="col-md-12 mb-3">
            <label for="rol_name" class="form-label">Nombre del Rol <span class="text-danger">*</span></label>
            <input type="text" 
                   class="form-control" 
                   id="rol_name" 
                   name="name" 
                   value="{{ $rol->name ?? '' }}" 
                   placeholder="Ej: Editor, Moderador, etc."
                   required>
        </div>

        <div class="col-md-12 mb-3">
            <h6 class="mb-3">Selecciona los permisos para este rol:</h6>
            
            @if($rol)
            @php
                $rolPermissions = $rol->permissions->pluck('id')->toArray();
            @endphp
            @else
            @php
                $rolPermissions = [];
            @endphp
            @endif

            <div class="row">
                @foreach($permissions as $grupo => $perms)
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 text-capitalize">
                                <i class="ti ti-folder"></i> {{ ucfirst($grupo) }}
                            </h6>
                        </div>
                        <div class="card-body">
                            @foreach($perms as $permission)
                            <div class="form-check mb-2">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="permissions[]" 
                                       value="{{ $permission->id }}" 
                                       id="perm_{{ $permission->id }}"
                                       {{ in_array($permission->id, $rolPermissions) ? 'checked' : '' }}>
                                <label class="form-check-label" for="perm_{{ $permission->id }}">
                                    {{ $permission->name }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-2">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="seleccionarTodos()">
                    <i class="ti ti-checkbox"></i> Seleccionar todos
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deseleccionarTodos()">
                    <i class="ti ti-square"></i> Deseleccionar todos
                </button>
            </div>
        </div>

        @if($rol)
        <div class="col-md-12 mb-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title">Información del Rol</h6>
                    <ul class="list-unstyled mb-0">
                        <li><strong>ID:</strong> {{ $rol->id }}</li>
                        <li><strong>Usuarios asignados:</strong> {{ $rol->users->count() }}</li>
                        <li><strong>Permisos actuales:</strong> {{ $rol->permissions->count() }}</li>
                    </ul>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="ti ti-x"></i> Cerrar
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="ti ti-device-floppy"></i> {{ $rol ? 'Actualizar' : 'Crear' }} Rol
        </button>
    </div>
</form>

<script>
    // Actualizar título del modal
    $('#tituloRolModal').text('{{ $titulo }}');

    function seleccionarTodos() {
        $('input[name="permissions[]"]').prop('checked', true);
    }

    function deseleccionarTodos() {
        $('input[name="permissions[]"]').prop('checked', false);
    }
</script>