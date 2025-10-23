// Modal para nuevo rol
function nuevoRolModal(rolId = null) {
    $.ajax({
        url: '/administracion/roles/modal',
        method: 'POST',
        data: { id: rolId },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            $('#rolModalContent').html(response);
            $('#rolModal').modal('show');
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo cargar el formulario'
            });
        }
    });
}

// Editar rol
function editarRol(id) {
    nuevoRolModal(id);
}

// Guardar rol (crear o editar)
function guardarRol() {
    const permisosSeleccionados = [];
    $('input[name="permissions[]"]:checked').each(function () {
        permisosSeleccionados.push($(this).val());
    });

    const formData = {
        id: $('#rol_id').val(),
        name: $('#rol_name').val(),
        permissions: permisosSeleccionados
    };

    $.ajax({
        url: '/administracion/roles/crear',
        method: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: response.message,
                    timer: 2000
                }).then(() => {
                    $('#rolModal').modal('hide');
                    location.reload(); // Recargar la página para ver los cambios
                });
            }
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                let errorMsg = '';
                for (let field in errors) {
                    errorMsg += errors[field].join('<br>') + '<br>';
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error de validación',
                    html: errorMsg
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Error al guardar el rol'
                });
            }
        }
    });
}

// Ver usuarios del rol
function verUsuariosRol(rolId, rolName) {
    $.ajax({
        url: '/administracion/roles/usuarios/modal',
        method: 'POST',
        data: { id: rolId },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            $('#tituloUsuariosRol').text('Usuarios del rol: ' + rolName);
            $('#usuariosRolModalContent').html(response);
            $('#usuariosRolModal').modal('show');
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo cargar la información'
            });
        }
    });
}

// Eliminar rol
function eliminarRol(rolId) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer. Los usuarios con este rol lo perderán.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/administracion/roles/' + rolId,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: response.message,
                            timer: 2000
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Error al eliminar el rol'
                    });
                }
            });
        }
    });
}

// ==================== GESTIÓN DE PERMISOS ====================

// Inicializar DataTable de permisos
function PermisosTable() {
    if ($.fn.DataTable.isDataTable('#PermisosTable')) {
        $('#PermisosTable').DataTable().clear().destroy();
    }
    if ($('#PermisosTable thead tr.filter').length) {
        $('#PermisosTable thead tr.filter').remove();
    }

    // Agregar fila de filtros
    $('#PermisosTable thead').prepend($('#PermisosTable thead tr').clone().addClass('filter'));
    $('#PermisosTable thead tr.filter th').addClass('px-1');

    $('#PermisosTable thead tr.filter th').each(function (index) {
        const col = $('#PermisosTable thead th').length / 2;
        if (index < col - 1) {
            const title = $(this).text();
            $(this).html('<input type="text" class="form-control form-control-sm" placeholder="' + title + '" />');
        }
    });

    // Evento de filtrado
    $('#PermisosTable thead tr.filter th input').on('keyup change', function () {
        const index = $(this).parent().index();
        const table = $('#PermisosTable').DataTable();
        table.column(index).search(this.value).draw();
    });

    // Inicializar DataTable
    const PermisosTable = $('#PermisosTable').DataTable({
        colReorder: true,
        dom: '<"top"Bf>rt<"bottom"lip>',
        pageLength: 50,
        order: [[0, 'asc']],
        buttons: [
            {
                text: '<i class="ti ti-plus"></i> Nuevo permiso',
                className: 'btn-primary rounded-pill',
                action: function (e, dt, node, config) {
                    if ($('table#PermisosTable').attr('data-add') == 1) {
                        nuevoPermisoModal();
                    } else {
                        no_privileges();
                    }
                }
            },
            {
                extend: 'excelHtml5',
                className: 'btn btn-success rounded-pill',
                text: '<i class="ti ti-file-type-xls"></i> Excel',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'pdf',
                className: 'btn btn-danger rounded-pill',
                text: '<i class="ti ti-file-type-pdf"></i> PDF'
            },
            {
                text: '<i class="ti ti-refresh"></i>',
                className: 'btn btn-info rounded-pill',
                action: function (e, dt, node, config) {
                    PermisosTable.ajax.reload();
                    $('.table-responsive').addClass('loader_ring');
                }
            }
        ],
        ajax: {
            method: 'POST',
            url: '/administracion/permisos_datatable',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            error: function () {
                $('.table-responsive').removeClass('loader_ring');
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "No se pudieron cargar los permisos",
                });
            },
            beforeSend: function () {
                $('.table-responsive').addClass('loader_ring');
            },
            complete: function () {
                $('.table-responsive').removeClass('loader_ring');
            },
        },
        rowId: 'id',
        columns: [
            { data: 'id', name: 'id', title: 'ID' },
            { data: 'name', name: 'name', title: 'Nombre' },
            { data: 'guard_name', name: 'guard_name', title: 'Guard' },
            { data: 'roles', name: 'roles', title: 'Roles', orderable: false },
            { data: 'roles_count', name: 'roles_count', title: '# Roles' },
            { data: 'created_at', name: 'created_at', title: 'Fecha Creación' },
            { data: 'action', name: 'action', title: 'Acciones', orderable: false, searchable: false }
        ],
        initComplete: function () {
            $('.table-responsive').removeClass('loader_ring');
            console.log('DataTable de permisos inicializada correctamente');
        }
    });
}

// Modal para nuevo permiso
function nuevoPermisoModal(permisoId = null) {
    $.ajax({
        url: '/administracion/permisos/modal',
        method: 'POST',
        data: { id: permisoId },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            $('#permisoModalContent').html(response);
            $('#permisoModal').modal('show');
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo cargar el formulario'
            });
        }
    });
}

// Ver permiso
function verPermiso(id) {
    nuevoPermisoModal(id);
    // Deshabilitar campos
    setTimeout(() => {
        $('#permisoModalContent input').prop('disabled', true);
        $('#permisoModalContent button[type="submit"]').hide();
    }, 500);
}

// Editar permiso
function editarPermiso(id) {
    nuevoPermisoModal(id);
}

// Guardar permiso
function guardarPermiso() {
    const formData = {
        id: $('#permiso_id').val(),
        name: $('#permiso_name').val()
    };

    $.ajax({
        url: '/administracion/permisos/crear',
        method: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: response.message,
                    timer: 2000
                });
                $('#permisoModal').modal('hide');
                $('#PermisosTable').DataTable().ajax.reload();
            }
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                let errorMsg = '';
                for (let field in errors) {
                    errorMsg += errors[field].join('<br>') + '<br>';
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error de validación',
                    html: errorMsg
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Error al guardar el permiso'
                });
            }
        }
    });
}

// Eliminar permiso
function eliminarPermiso(permisoId) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/administracion/permisos/' + permisoId,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: response.message,
                            timer: 2000
                        });
                        $('#PermisosTable').DataTable().ajax.reload();
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Error al eliminar el permiso'
                    });
                }
            });
        }
    });
}

// Función de sin privilegios
function no_privileges() {
    Swal.fire({
        icon: 'warning',
        title: 'Sin privilegios',
        text: 'No tienes permisos para realizar esta acción',
        timer: 3000
    });
}