{{-- resources/views/administracion/roles.blade.php --}}
<x-app-layout>
    <div class="page-container">
        <div class="page-title-head d-flex align-items-center gap-2">
            <div class="flex-grow-1">
                <h4 class="fs-17 mb-0">Gestión de Roles y Permisos</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0 fs-13">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Administración</a></li>
                    <li class="breadcrumb-item active">Roles y Permisos</li>
                </ol>
            </div>
        </div>

        {{-- Tabs Navigation --}}
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#roles-tab" role="tab">
                            <i class="ti ti-shield-check"></i>
                            <span class="d-none d-md-inline ms-2">Roles</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#permisos-tab" role="tab">
                            <i class="ti ti-key"></i>
                            <span class="d-none d-md-inline ms-2">Permisos</span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content p-3">
                    {{-- TAB 1: ROLES --}}
                    <div class="tab-pane active" id="roles-tab" role="tabpanel">
                        @can('crear-roles')
                        <div class="row mb-3">
                            <div class="col-12">
                                <button class="btn btn-primary" onclick="nuevoRolModal()">
                                    <i class="ti ti-plus"></i> Crear Nuevo Rol
                                </button>
                            </div>
                        </div>
                        @endcan

                        <div class="row">
                            @forelse($roles as $role)
                            <div class="col-md-6 col-xl-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between mb-3">
                                            <div>
                                                <h4 class="card-title mb-1">
                                                    <i class="ti ti-shield-check text-primary"></i>
                                                    {{ $role->name }}
                                                </h4>
                                                <p class="text-muted fs-13 mb-0">
                                                    <i class="ti ti-users"></i>
                                                    {{ $role->users->count() }} {{ $role->users->count() == 1 ? 'usuario' : 'usuarios' }}
                                                </p>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ti ti-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    @can('editar-roles')
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0);" onclick="editarRol({{ $role->id }})">
                                                            <i class="ti ti-pencil"></i> Editar Rol
                                                        </a>
                                                    </li>
                                                    @endcan
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0);" onclick="verUsuariosRol({{ $role->id }}, '{{ $role->name }}')">
                                                            <i class="ti ti-users"></i> Ver Usuarios
                                                        </a>
                                                    </li>
                                                    @can('eliminar-roles')
                                                    @if(!in_array($role->name, ['Administrador', 'Usuario', 'Supervisor']))
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="eliminarRol({{ $role->id }})">
                                                            <i class="ti ti-trash"></i> Eliminar Rol
                                                        </a>
                                                    </li>
                                                    @endif
                                                    @endcan
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <h6 class="fs-14 mb-2">
                                                <i class="ti ti-lock"></i> Permisos asignados ({{ $role->permissions->count() }}):
                                            </h6>
                                            <div style="max-height: 180px; overflow-y: auto;">
                                                @forelse($role->permissions as $permission)
                                                    <span class="badge bg-primary-subtle text-primary me-1 mb-1">
                                                        {{ $permission->name }}
                                                    </span>
                                                @empty
                                                    <p class="text-muted fs-13 mb-0">
                                                        <em>Sin permisos asignados</em>
                                                    </p>
                                                @endforelse
                                            </div>
                                        </div>

                                        <hr>

                                        <div class="mt-3">
                                            <h6 class="fs-14 mb-2">
                                                <i class="ti ti-user-circle"></i> Usuarios con este rol:
                                            </h6>
                                            <div>
                                                @forelse($role->users->take(3) as $user)
                                                    <div class="d-flex align-items-center mb-2">
                                                        <img src="{{ asset('assets/images/avatar_generico.svg') }}"
                                                             class="rounded-circle me-2"
                                                             width="28"
                                                             height="28"
                                                             alt="{{ $user->name }}">
                                                        <span class="fs-13">{{ $user->name }}</span>
                                                    </div>
                                                @empty
                                                    <p class="text-muted fs-13 mb-0">
                                                        <em>Sin usuarios asignados</em>
                                                    </p>
                                                @endforelse

                                                @if($role->users->count() > 3)
                                                    <a href="javascript:void(0);"
                                                       class="fs-13 text-primary fw-semibold"
                                                       onclick="verUsuariosRol({{ $role->id }}, '{{ $role->name }}')">
                                                        <i class="ti ti-arrow-right"></i> Ver todos ({{ $role->users->count() }})
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    @if(in_array($role->name, ['Administrador', 'Usuario', 'Supervisor']))
                                    <div class="card-footer bg-light">
                                        <small class="text-muted">
                                            <i class="ti ti-info-circle"></i> Rol del sistema (no se puede eliminar)
                                        </small>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body text-center py-5">
                                        <i class="ti ti-shield-off" style="font-size: 4rem; opacity: 0.3;"></i>
                                        <h5 class="mt-3">No hay roles creados</h5>
                                        <p class="text-muted">Comienza creando un nuevo rol para gestionar los permisos de los usuarios.</p>
                                        @can('crear-roles')
                                        <button class="btn btn-primary mt-2" onclick="nuevoRolModal()">
                                            <i class="ti ti-plus"></i> Crear Primer Rol
                                        </button>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- TAB 2: PERMISOS --}}
                    <div class="tab-pane" id="permisos-tab" role="tabpanel">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title mb-2">Permisos del Sistema</h4>
                                        <p class="text-muted fs-12 mb-3">
                                            Gestiona los permisos que pueden ser asignados a los roles. Los permisos definen las acciones específicas que los usuarios pueden realizar.
                                        </p>

                                        <div id="permisos-datatable_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div id="tabla-permisos-container">
                                                        <div class="table-responsive">
                                                            <table id="PermisosTable"
                                                                   class="table table-striped dt-responsive nowrap w-100 dataTable no-footer dtr-inline"
                                                                   data-add="{{ auth()->user()->can('crear-roles') ? 1 : 0 }}">
                                                                <thead>
                                                                    <tr>
                                                                        <th>ID</th>
                                                                        <th>Nombre del Permiso</th>
                                                                        <th>Guard</th>
                                                                        <th>Roles</th>
                                                                        <th># Roles</th>
                                                                        <th>Fecha Creación</th>
                                                                        <th>Acciones</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Nuevo/Editar Rol --}}
    <div class="modal fade" id="rolModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-dark text-light">
                    <h5 class="modal-title">
                        <i class="ti ti-shield"></i> <span id="tituloRolModal">Nuevo Rol</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body" id="rolModalContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="text-muted mt-2">Cargando formulario...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Usuarios por Rol --}}
    <div class="modal fade" id="usuariosRolModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-dark text-light">
                    <h5 class="modal-title">
                        <i class="ti ti-users"></i> <span id="tituloUsuariosRol">Usuarios del Rol</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body" id="usuariosRolModalContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="text-muted mt-2">Cargando usuarios...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Nuevo/Editar Permiso --}}
    <div class="modal fade" id="permisoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-dark text-light">
                    <h5 class="modal-title">
                        <i class="ti ti-key"></i> <span id="tituloPermisoModal">Nuevo Permiso</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body" id="permisoModalContent">
                    <p class="text-muted">Cargando...</p>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="scripts">
        <script src="{{ asset('assets/js/roles.js') }}"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                // Inicializar DataTable de permisos cuando se cambia al tab
                $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                    if ($(e.target).attr('href') === '#permisos-tab') {
                        if (!$.fn.DataTable.isDataTable('#PermisosTable')) {
                            PermisosTable();
                        }
                    }
                });
            });
        </script>
    </x-slot>
</x-app-layout>
