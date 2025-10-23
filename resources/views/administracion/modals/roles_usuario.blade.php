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

        <div class="row">
            @foreach($roles as $role)
            <div class="col-md-6 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div>
                                <h4 class="card-title mb-1">{{ $role->name }}</h4>
                                <p class="text-muted fs-13 mb-0">
                                    <i class="ti ti-users"></i> 
                                    {{ $role->users->count() }} {{ $role->users->count() == 1 ? 'usuario' : 'usuarios' }}
                                </p>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    @can('editar-roles')
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0);" onclick="editarRol({{ $role->id }})">
                                            <i class="ti ti-pencil"></i> Editar
                                        </a>
                                    </li>
                                    @endcan
                                    @can('eliminar-roles')
                                    @if(!in_array($role->name, ['Administrador', 'Usuario']))
                                    <li>
                                        <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="eliminarRol({{ $role->id }})">
                                            <i class="ti ti-trash"></i> Eliminar
                                        </a>
                                    </li>
                                    @endif
                                    @endcan
                                </ul>
                            </div>
                        </div>

                        <h6 class="mb-2">Permisos asignados:</h6>
                        <div class="mb-3" style="max-height: 200px; overflow-y: auto;">
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

                        <div class="mt-3">
                            <strong class="fs-13">Usuarios con este rol:</strong>
                            <div class="mt-2">
                                @forelse($role->users->take(3) as $user)
                                    <div class="d-flex align-items-center mb-2">
                                        <img src="{{ asset('assets/images/avatar_generico.svg') }}" 
                                             class="rounded-circle me-2" 
                                             width="24" 
                                             height="24" 
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
                                       class="fs-13 text-primary" 
                                       onclick="verUsuariosRol({{ $role->id }}, '{{ $role->name }}')">
                                        Ver todos ({{ $role->users->count() }})
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @can('crear-roles')
        <div class="row">
            <div class="col-12">
                <button class="btn btn-primary" onclick="nuevoRolModal()">
                    <i class="ti ti-plus"></i> Crear Nuevo Rol
                </button>
            </div>
        </div>
        @endcan
    </div>

    {{-- Modal: Nuevo/Editar Rol --}}
    <div class="modal fade" id="rolModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark text-light">
                    <h5 class="modal-title">
                        <i class="ti ti-shield"></i> <span id="tituloRolModal">Nuevo Rol</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="rolModalContent">
                    <p class="text-muted">Cargando...</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Usuarios por Rol --}}
    <div class="modal fade" id="usuariosRolModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark text-light">
                    <h5 class="modal-title">
                        <i class="ti ti-users"></i> <span id="tituloUsuariosRol">Usuarios del Rol</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="usuariosRolModalContent">
                    <p class="text-muted">Cargando...</p>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="scripts">
        <script src="{{ asset('assets/js/roles.js') }}"></script>
    </x-slot>
</x-app-layout>