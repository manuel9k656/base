<div class="sidenav-menu">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="logo">
        <span class="logo-light">
            <span class="logo-lg"><img src="{{ asset('assets/images/logo.png') }}" alt="logo"></span>
            <span class="logo-sm"><img src="{{ asset('assets/images/logo-sm.png') }}" alt="small logo"></span>
        </span>
        <span class="logo-dark">
            <span class="logo-lg"><img src="{{ asset('assets/images/logo-dark.png') }}" alt="dark logo"></span>
            <span class="logo-sm"><img src="{{ asset('assets/images/logo-sm.png') }}" alt="small logo"></span>
        </span>
    </a>

    <!-- Sidebar Hover Menu Toggle Button -->
    <button class="button-sm-hover">
        <i class="ti ti-circle align-middle"></i>
    </button>

    <!-- Full Sidebar Menu Close Button -->
    <button class="button-close-fullsidebar">
        <i class="ti ti-x align-middle"></i>
    </button>

    <div data-simplebar>
        <!--- Sidenav Menu -->
        <ul class="side-nav">
            <li class="side-nav-title">Navegación Principal</li>

            <li class="side-nav-item">
                <a href="{{ route('dashboard') }}" class="side-nav-link">
                    <span class="menu-icon"><i class="ti ti-dashboard"></i></span>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>

            @can('ver-usuarios')
            <li class="side-nav-title mt-2">Administración</li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarAdministracion" aria-expanded="false" aria-controls="sidebarAdministracion" class="side-nav-link">
                    <span class="menu-icon"><i class="ti ti-settings"></i></span>
                    <span class="menu-text">Administración</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarAdministracion">
                    <ul class="sub-menu">
                        @can('ver-usuarios')
                        <li class="side-nav-item">
                            <a href="{{ route('administracion.usuarios') }}" class="side-nav-link">
                                <span class="menu-text">Usuarios</span>
                            </a>
                        </li>
                        @endcan
                        
                        @can('ver-roles')
                        <li class="side-nav-item">
                            <a href="{{ route('administracion.roles') }}" class="side-nav-link">
                                <span class="menu-text">Roles y Permisos</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

        </ul>

        <!-- Help Box -->
        <div class="help-box text-center">
            <h5 class="fw-semibold fs-16">¿Necesitas ayuda?</h5>
            <p class="mb-3 text-muted">Contacta al administrador del sistema</p>
            <a href="javascript: void(0);" class="btn btn-primary btn-sm">Soporte</a>
        </div>

        <div class="clearfix"></div>
    </div>
</div>