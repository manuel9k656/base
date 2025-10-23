{{-- Modal contenido para ver usuarios de un rol específico --}}
<div class="modal-body">
    <div class="alert alert-info mb-3">
        <i class="ti ti-shield-check me-2"></i>
        <strong>Rol:</strong> {{ $rol->name }}
    </div>

    @if($rol->users->count() > 0)
        <div class="mb-3">
            <h6 class="mb-3">
                <i class="ti ti-users"></i>
                Usuarios con este rol ({{ $rol->users->count() }})
            </h6>

            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Fecha Registro</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rol->users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('assets/images/avatar_generico.svg') }}"
                                         class="rounded-circle me-2"
                                         width="32"
                                         height="32"
                                         alt="{{ $user->name }}">
                                    <strong>{{ $user->name }}</strong>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <small class="text-muted">
                                    {{ $user->created_at->format('d/m/Y H:i') }}
                                </small>
                            </td>
                            <td>
                                @if($user->email_verified_at)
                                    <span class="badge bg-success">
                                        <i class="ti ti-check"></i> Verificado
                                    </span>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="ti ti-clock"></i> Pendiente
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="alert alert-secondary mb-0">
            <small>
                <i class="ti ti-info-circle me-1"></i>
                <strong>Nota:</strong> Para gestionar los roles de un usuario específico, ve a la sección de Usuarios.
            </small>
        </div>
    @else
        <div class="alert alert-warning mb-0">
            <i class="ti ti-alert-circle me-2"></i>
            <strong>Sin usuarios asignados</strong>
            <p class="mb-0">No hay usuarios con el rol <strong>{{ $rol->name }}</strong> actualmente.</p>
        </div>
    @endif
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
        <i class="ti ti-x"></i> Cerrar
    </button>
</div>
