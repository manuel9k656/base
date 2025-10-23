// Función para inicializar la tabla de usuarios
function UsuariosTable() {
    if ($.fn.DataTable.isDataTable('#UsuariosTable')) {
        $('#UsuariosTable').DataTable().clear().destroy();
    }
    if ($('#UsuariosTable thead tr.filter').length) {
        $('#UsuariosTable thead tr.filter').remove();
    }

    // Agregar fila de filtros
    $('#UsuariosTable thead').prepend($('#UsuariosTable thead tr').clone().addClass('filter'));
    $('#UsuariosTable thead tr.filter th').addClass('px-1');

    $('#UsuariosTable thead tr.filter th').each(function (index) {
        const col = $('#UsuariosTable thead th').length / 2;
        if (index < col - 1) {
            const title = $(this).text();
            $(this).html('<input type="text" class="form-control form-control-sm" placeholder="' + title + '" />');
        }
    });

    // Evento de filtrado
    $('#UsuariosTable thead tr.filter th input').on('keyup change', function () {
        const index = $(this).parent().index();
        const table = $('#UsuariosTable').DataTable();
        table.column(index).search(this.value).draw();
    });

    // Inicializar DataTable
    const UsuariosTable = $('#UsuariosTable').DataTable({
        colReorder: true,
        dom: '<"top"Bf>rt<"bottom"lip>',
        pageLength: 50,
        order: [[0, 'asc']],
        buttons: [
            {
                text: '<i class="ti ti-plus"></i> Nuevo usuario',
                className: ' btn-primary rounded-pill',
                action: function (e, dt, node, config) {
                    if ($('table#UsuariosTable').attr('data-add') == 1) {
                        nuevoUsuarioModal();
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
            // {
            //     extend: 'print',
            //     className: 'btn btn-outline-success',
            //     text: '<i class="ti ti-printer"></i> Print'
            // },
            // {
            //     extend: 'copy',
            //     className: 'btn btn-outline-success',
            //     text: '<i class="ti ti-copy"></i> Copiar'
            // },
            {
                text: '<i class="ti ti-refresh"></i>',
                className: 'btn btn-info rounded-pill',
                action: function (e, dt, node, config) {
                    UsuariosTable.ajax.reload();
                    $('.table-responsive').addClass('loader_ring');
                }
            }
        ],
        ajax: {
            method: 'POST',
            url: '/administracion/usuarios_datatable',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            error: function () {
                $('.table-responsive').removeClass('loader_ring');
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "No se pudieron cargar los usuarios",
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
            { data: 'email', name: 'email', title: 'Email' },
            { data: 'roles', name: 'roles', title: 'Roles', orderable: false },
            { data: 'created_at', name: 'created_at', title: 'Fecha Registro' },
            { data: 'email_verified_at', name: 'email_verified_at', title: 'Estado' },
            { data: 'action', name: 'action', title: 'Acciones', orderable: false, searchable: false }
        ],
        initComplete: function () {
            $('.table-responsive').removeClass('loader_ring');
            console.log('DataTable inicializada correctamente');
        }
    });
}

// Modal para nuevo usuario
async function nuevoUsuarioModal(userId = null) {
    const response = await $.ajax({
        url: '/administracion/usuarios/modal',
        method: 'POST',
        data: { id: userId },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            $('#nuevoUsuarioModalContent').html(response);
            $('#nuevoUsuarioModal').modal('show');
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

// Ver usuario
async function verUsuario(id) {
    await nuevoUsuarioModal(id);
    // Deshabilitar todos los campos del formulario
    $('#nuevoUsuarioModalContent input, #nuevoUsuarioModalContent select, #nuevoUsuarioModalContent textarea').prop('disabled', true);
    $('#nuevoUsuarioModalContent button[type="submit"]').hide();
    $('#nuevoUsuarioModalContent .alert').hide();

    // setTimeout(() => {
    // }, 900);
}

// Editar usuario
function editarUsuario(id) {
    nuevoUsuarioModal(id);
}

// Guardar usuario (crear o editar)
function guardarUsuario() {
    const formData = {
        id: $('#usuario_id').val(),
        name: $('#name').val(),
        email: $('#email').val(),
        password: $('#password').val(),
        password_confirmation: $('#password_confirmation').val()
    };

    $.ajax({
        url: '/administracion/usuarios/crear',
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
                $('#nuevoUsuarioModal').modal('hide');
                $('#UsuariosTable').DataTable().ajax.reload();
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
                    text: xhr.responseJSON?.message || 'Error al guardar el usuario'
                });
            }
        }
    });
}

// Modal de roles del usuario
function abrirModalRolesUsuario(userId, userName) {
    $.ajax({
        url: '/administracion/usuarios/roles/modal',
        method: 'POST',
        data: { id: userId },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            $('#tituloRolUsuarios').text('Roles de ' + userName);
            $('#RolesUsuarioModalContent').html(response);
            $('#RolesUsuarioModal').modal('show');
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo cargar los roles'
            });
        }
    });
}

// Guardar roles del usuario
function guardarRolesUsuario(userId) {
    const rolesSeleccionados = [];
    $('input[name="roles[]"]:checked').each(function () {
        rolesSeleccionados.push($(this).val());
    });

    $.ajax({
        url: '/administracion/usuarios/' + userId + '/roles',
        method: 'POST',
        data: { roles: rolesSeleccionados },
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
                $('#RolesUsuarioModal').modal('hide');
                $('#UsuariosTable').DataTable().ajax.reload();
            }
        },
        error: function (xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: xhr.responseJSON?.message || 'Error al asignar roles'
            });
        }
    });
}

// Cambiar contraseña
function cambiarPassword(userId, userName) {
    Swal.fire({
        title: 'Cambiar contraseña de ' + userName,
        html: `
            <div class="mb-3">
                <label class="form-label">Nueva contraseña</label>
                <input type="password" id="swal-password" class="form-control" placeholder="Mínimo 8 caracteres">
            </div>
            <div class="mb-3">
                <label class="form-label">Confirmar contraseña</label>
                <input type="password" id="swal-password-confirm" class="form-control" placeholder="Repite la contraseña">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Cambiar contraseña',
        cancelButtonText: 'Cancelar',
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-secondary'
        },
        preConfirm: () => {
            const password = document.getElementById('swal-password').value;
            const passwordConfirm = document.getElementById('swal-password-confirm').value;

            if (!password || password.length < 8) {
                Swal.showValidationMessage('La contraseña debe tener al menos 8 caracteres');
                return false;
            }

            if (password !== passwordConfirm) {
                Swal.showValidationMessage('Las contraseñas no coinciden');
                return false;
            }

            return { password: password, password_confirmation: passwordConfirm };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/administracion/usuarios/' + userId + '/password',
                method: 'POST',
                data: result.value,
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
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Error al cambiar la contraseña'
                    });
                }
            });
        }
    });
}

// Eliminar usuario
function eliminarUsuario(userId) {
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
                url: '/administracion/usuarios/' + userId,
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
                        $('#UsuariosTable').DataTable().ajax.reload();
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Error al eliminar el usuario'
                    });
                }
            });
        }
    });
}

// Función para mostrar mensaje de sin privilegios
function no_privileges() {
    Swal.fire({
        icon: 'warning',
        title: 'Sin privilegios',
        text: 'No tienes permisos para realizar esta acción',
        timer: 3000
    });
}

// ==================== GESTIÓN DE PERMISOS DE USUARIOS ====================

// Modal de permisos del usuario
function abrirModalPermisosUsuario(userId, userName) {
    $.ajax({
        url: '/administracion/usuarios/permisos/modal',
        method: 'POST',
        data: { id: userId },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            $('#tituloPermisosUsuarios').text('Permisos de ' + userName);
            $('#PermisosUsuarioModalContent').html(response);
            $('#PermisosUsuarioModal').modal('show');
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo cargar los permisos'
            });
        }
    });
}

// Guardar permisos del usuario
function guardarPermisosUsuario(userId) {
    const permisosSeleccionados = [];
    $('input[name="permissions[]"]:checked:not(:disabled)').each(function () {
        permisosSeleccionados.push($(this).val());
    });

    $.ajax({
        url: '/administracion/usuarios/' + userId + '/permisos',
        method: 'POST',
        data: { permissions: permisosSeleccionados },
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
                $('#PermisosUsuarioModal').modal('hide');
                $('#UsuariosTable').DataTable().ajax.reload();
            }
        },
        error: function (xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: xhr.responseJSON?.message || 'Error al asignar permisos'
            });
        }
    });
}