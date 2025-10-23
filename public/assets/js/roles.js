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