{{-- Modal contenido para asignar permisos individuales a usuario --}}
<form id="permisosUsuarioForm">
    <input type="hidden" id="usuario_id_permisos" value="{{ $user->id }}">

    <div class="alert alert-info">
        <i class="ti ti-info-circle me-2"></i>
        <strong>Usuario:</strong> {{ $user->name }} ({{ $user->email }})
    </div>

    <div class="alert alert-warning mb-3">
        <i class="ti ti-alert-triangle me-2"></i>
        <strong>Importante:</strong> Los permisos individuales se <strong>suman</strong> a los permisos heredados de los roles.
        <br>
        <small>Los permisos en <span class="badge bg-success">verde</span> provienen de roles asignados y no se pueden desactivar aquí.</small>
    </div>

    @if($user->roles->count() > 0)
    <div class="mb-3">
        <h6 class="fw-bold">Roles del usuario:</h6>
        @foreach($user->roles as $role)
            <span class="badge bg-primary me-1">{{ $role->name }}</span>
        @endforeach
    </div>
    @endif

    <div class="mb-3">
        <label class="form-label fw-bold">Permisos directos del usuario:</label>
        <p class="text-muted fs-13 mb-3">Selecciona permisos adicionales que este usuario tendrá, independientemente de sus roles.</p>

        @if($permissions->count() > 0)
            <div class="accordion" id="accordionPermisos">
                @foreach($permissions as $category => $perms)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading{{ $loop->index }}">
                            <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $loop->index }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                                <i class="ti ti-folder me-2"></i>
                                <strong>{{ ucfirst($category) }}</strong>
                                <span class="badge bg-secondary ms-2">{{ $perms->count() }} permisos</span>
                            </button>
                        </h2>
                        <div id="collapse{{ $loop->index }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="heading{{ $loop->index }}">
                            <div class="accordion-body">
                                <div class="row g-2">
                                    @foreach($perms as $permission)
                                        @php
                                            $isDirectPermission = in_array($permission->name, $userDirectPermissions);
                                            $isFromRole = in_array($permission->name, $rolePermissions);
                                            $isDisabled = $isFromRole && !$isDirectPermission;
                                        @endphp
                                        <div class="col-md-6">
                                            <div class="form-check p-2 border rounded {{ $isDirectPermission ? 'bg-primary-subtle' : ($isFromRole ? 'bg-success-subtle' : '') }}">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    name="permissions[]"
                                                    value="{{ $permission->name }}"
                                                    id="perm_{{ $permission->id }}"
                                                    {{ $isDirectPermission ? 'checked' : '' }}
                                                    {{ $isDisabled ? 'disabled' : '' }}
                                                >
                                                <label class="form-check-label w-100" for="perm_{{ $permission->id }}">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <span>{{ $permission->name }}</span>
                                                        @if($isFromRole)
                                                            <span class="badge bg-success" style="font-size: 0.7rem;">
                                                                <i class="ti ti-shield-check"></i> Rol
                                                            </span>
                                                        @endif
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-warning mt-3">
                <i class="ti ti-alert-triangle me-2"></i>
                No hay permisos disponibles. Crea permisos primero desde el módulo de Roles y Permisos.
            </div>
        @endif
    </div>

    @if($permissions->count() > 0)
        <div class="alert alert-secondary">
            <small>
                <i class="ti ti-info-circle me-1"></i>
                <strong>Leyenda:</strong>
                <br>
                • <span class="badge bg-primary-subtle text-primary">Azul</span> = Permiso directo del usuario (puedes quitar)
                <br>
                • <span class="badge bg-success-subtle text-success">Verde</span> = Heredado de rol (debes quitarlo del rol)
                <br>
                • Sin color = No asignado
            </small>
        </div>

        <div class="modal-footer border-top">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="ti ti-x"></i> Cancelar
            </button>
            <button type="button" class="btn btn-primary" onclick="guardarPermisosUsuario({{ $user->id }})">
                <i class="ti ti-check"></i> Guardar permisos
            </button>
        </div>
    @endif
</form>

<script>
    // Agregar efecto visual al seleccionar/deseleccionar checkboxes
    document.querySelectorAll('input[name="permissions[]"]:not(:disabled)').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const formCheck = this.closest('.form-check');
            if (this.checked) {
                formCheck.classList.add('bg-primary-subtle');
            } else {
                formCheck.classList.remove('bg-primary-subtle');
            }
        });
    });

    // Botones para seleccionar/deseleccionar todos en una categoría
    document.querySelectorAll('.accordion-button').forEach(button => {
        const accordionBody = button.closest('.accordion-item').querySelector('.accordion-body');

        // Agregar botones de ayuda
        button.addEventListener('shown.bs.collapse', function() {
            if (!accordionBody.querySelector('.select-helpers')) {
                const helpers = document.createElement('div');
                helpers.className = 'select-helpers mb-2 text-end';
                helpers.innerHTML = `
                    <button type="button" class="btn btn-sm btn-outline-primary me-1" onclick="selectAllInCategory(this, true)">
                        <i class="ti ti-check"></i> Seleccionar todos
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAllInCategory(this, false)">
                        <i class="ti ti-x"></i> Deseleccionar todos
                    </button>
                `;
                accordionBody.insertBefore(helpers, accordionBody.firstChild);
            }
        });
    });

    function selectAllInCategory(btn, select) {
        const accordionBody = btn.closest('.accordion-body');
        const checkboxes = accordionBody.querySelectorAll('input[name="permissions[]"]:not(:disabled)');
        checkboxes.forEach(cb => {
            cb.checked = select;
            const formCheck = cb.closest('.form-check');
            if (select) {
                formCheck.classList.add('bg-primary-subtle');
            } else {
                formCheck.classList.remove('bg-primary-subtle');
            }
        });
    }
</script>
