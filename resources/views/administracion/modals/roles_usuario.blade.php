{{-- Modal contenido para asignar roles a usuario --}}
<form id="rolesUsuarioForm">
    <input type="hidden" id="usuario_id_roles" value="{{ $user->id }}">

    <div class="alert alert-info">
        <i class="ti ti-info-circle me-2"></i>
        <strong>Usuario:</strong> {{ $user->name }} ({{ $user->email }})
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Selecciona los roles para este usuario:</label>

        @if($roles->count() > 0)
            <div class="row g-3 mt-2">
                @foreach($roles as $role)
                    <div class="col-md-6">
                        <div class="card border {{ in_array($role->id, $userRoles) ? 'border-primary bg-primary-subtle' : '' }}">
                            <div class="card-body p-3">
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="roles[]"
                                        value="{{ $role->name }}"
                                        id="role_{{ $role->id }}"
                                        data-role-id="{{ $role->id }}"
                                        {{ in_array($role->id, $userRoles) ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label w-100" for="role_{{ $role->id }}">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <h6 class="mb-0">{{ $role->name }}</h6>
                                                <small class="text-muted">
                                                    <i class="ti ti-shield-check"></i>
                                                    {{ $role->permissions->count() }}
                                                    {{ $role->permissions->count() == 1 ? 'permiso' : 'permisos' }}
                                                </small>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                @if($role->permissions->count() > 0)
                                    <div class="mt-2 pt-2 border-top">
                                        <small class="text-muted d-block mb-1"><strong>Permisos:</strong></small>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($role->permissions->take(3) as $permission)
                                                <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.7rem;">
                                                    {{ $permission->name }}
                                                </span>
                                            @endforeach
                                            @if($role->permissions->count() > 3)
                                                <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.7rem;">
                                                    +{{ $role->permissions->count() - 3 }} más
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-warning mt-3">
                <i class="ti ti-alert-triangle me-2"></i>
                No hay roles disponibles. Crea roles primero desde el módulo de Roles y Permisos.
            </div>
        @endif
    </div>

    @if($roles->count() > 0)
        <div class="alert alert-secondary">
            <small>
                <i class="ti ti-info-circle me-1"></i>
                <strong>Nota:</strong> Puedes asignar múltiples roles a un usuario. Los permisos se acumularán.
            </small>
        </div>

        <div class="modal-footer border-top">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="ti ti-x"></i> Cancelar
            </button>
            <button type="button" class="btn btn-primary" onclick="guardarRolesUsuario({{ $user->id }})">
                <i class="ti ti-check"></i> Guardar cambios
            </button>
        </div>
    @endif
</form>

<script>
    // Agregar efecto visual al seleccionar/deseleccionar checkboxes
    document.querySelectorAll('input[name="roles[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const card = this.closest('.card');
            if (this.checked) {
                card.classList.add('border-primary', 'bg-primary-subtle');
            } else {
                card.classList.remove('border-primary', 'bg-primary-subtle');
            }
        });
    });
</script>
