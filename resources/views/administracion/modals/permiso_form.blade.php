{{-- resources/views/administracion/modals/permiso_form.blade.php --}}
<form id="formPermiso" onsubmit="event.preventDefault(); guardarPermiso();">
    <input type="hidden" id="permiso_id" value="{{ $permiso->id ?? '' }}">

    <div class="row">
        <div class="col-md-12 mb-3">
            <label for="permiso_name" class="form-label">
                Nombre del permiso <span class="text-danger">*</span>
            </label>
            <input type="text"
                   class="form-control"
                   id="permiso_name"
                   name="name"
                   value="{{ $permiso->name ?? '' }}"
                   placeholder="Ej: crear-usuarios, editar-posts, ver-reportes"
                   required>
            <small class="text-muted">
                Usa un formato descriptivo con guiones: <code>verbo-recurso</code>
            </small>
        </div>

        @if($permiso)
        <div class="col-md-12 mb-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title mb-3">
                        <i class="ti ti-info-circle"></i> Información adicional
                    </h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <strong>ID:</strong> {{ $permiso->id }}
                        </li>
                        <li class="mb-2">
                            <strong>Guard:</strong>
                            <span class="badge bg-secondary">{{ $permiso->guard_name }}</span>
                        </li>
                        <li class="mb-2">
                            <strong>Fecha de creación:</strong>
                            {{ $permiso->created_at->format('d/m/Y H:i') }}
                        </li>
                        <li class="mb-2">
                            <strong>Roles que usan este permiso:</strong>
                            @forelse($permiso->roles as $role)
                                <span class="badge bg-success me-1">{{ $role->name }}</span>
                            @empty
                                <span class="badge bg-secondary">Sin roles asignados</span>
                            @endforelse
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        @else
        <div class="col-md-12 mb-3">
            <div class="alert alert-info">
                <i class="ti ti-bulb me-2"></i>
                <strong>Convención de nombres:</strong>
                <ul class="mb-0 mt-2">
                    <li><code>ver-usuarios</code> - Para visualizar recursos</li>
                    <li><code>crear-usuarios</code> - Para crear nuevos registros</li>
                    <li><code>editar-usuarios</code> - Para modificar registros</li>
                    <li><code>eliminar-usuarios</code> - Para borrar registros</li>
                </ul>
            </div>
        </div>
        @endif

        @if($permiso)
        <div class="col-md-12 mb-3">
            <div class="alert alert-warning">
                <i class="ti ti-alert-triangle me-2"></i>
                <strong>Advertencia:</strong> Al cambiar el nombre del permiso, afectará a todos los roles que lo usan.
            </div>
        </div>
        @endif
    </div>

    <div class="modal-footer border-top mt-3">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="ti ti-x"></i> Cancelar
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="ti ti-device-floppy"></i> {{ $permiso ? 'Actualizar' : 'Crear' }} Permiso
        </button>
    </div>
</form>

<script>
    // Actualizar título del modal
    $('#tituloPermisoModal').text('{{ $titulo }}');

    // Convertir a minúsculas y reemplazar espacios por guiones
    $('#permiso_name').on('input', function() {
        let value = $(this).val();
        value = value.toLowerCase().replace(/\s+/g, '-');
        $(this).val(value);
    });
</script>
