<x-app-layout>
    <div class="page-container">
        <div class="page-title-head d-flex align-items-center gap-2">
            <div class="flex-grow-1">
                <h4 class="fs-17 mb-0">Gestión de Usuarios</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0 fs-13">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Administración</a></li>
                    <li class="breadcrumb-item active">Usuarios</li>
                </ol>
            </div>
        </div>



        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-2">Usuarios del Sistema</h4>
                        <p class="text-muted fs-12">
                            Sección para gestionar los usuarios del sistema. Aquí podrás ver, editar y eliminar usuarios
                            existentes, así como agregar nuevos usuarios al sistema. Utiliza las opciones de búsqueda y
                            filtrado para encontrar rápidamente a los usuarios que necesitas gestionar.
                        </p>

                        <div id="basic-datatable_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div id="tabla-container" >
                                        <div class="table-responsive">
                                            <table id="UsuariosTable" class="table table-striped dt-responsive nowrap w-100 dataTable no-footer dtr-inline"
                                                   data-add="{{ auth()->user()->can('crear-usuarios') ? 1 : 0 }}">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Nombre</th>
                                                        <th>Email</th>
                                                        <th>Roles</th>
                                                        <th>Fecha Registro</th>
                                                        <th>Estado</th>
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

    {{-- Modal: Nuevo/Editar Usuario --}}
    <div class="modal fade" id="nuevoUsuarioModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-dark text-light">
                    <h5 class="modal-title">
                        <i class="ti ti-user"></i> <span id="tituloUsuarioModal">Nuevo Usuario</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body" id="nuevoUsuarioModalContent">
                    <p class="text-muted">Cargando...</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Roles del Usuario --}}
    <div class="modal fade" id="RolesUsuarioModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-dark text-light">
                    <h5 class="modal-title">
                        <i class="ti ti-shield-check"></i> <span id="tituloRolUsuarios">Roles del Usuario</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body" id="RolesUsuarioModalContent">
                    <p class="text-muted">Cargando...</p>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="scripts">
        <script src="{{ asset('assets/js/administracion.js') }}"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                UsuariosTable();
            });
        </script>
    </x-slot>
</x-app-layout>