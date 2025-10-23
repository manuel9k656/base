 <header class="app-topbar">
    <div class="page-container topbar-menu">
        <div class="d-flex align-items-center gap-2">

            <!-- Brand Logo -->
            <a href="index.html" class="logo">
                <span class="logo-light">
                    <span class="logo-lg"><img src="{{ asset('assets/images/logo.ico') }}" alt="logo"></span>
                    <span class="logo-sm"><img src="{{ asset('assets/images/logo.ico') }}" alt="small logo"></span>
                </span>

                <span class="logo-dark">
                    <span class="logo-lg"><img src="{{ asset('assets/images/logo.ico') }}" alt="dark logo"></span>
                    <span class="logo-sm"><img src="{{ asset('assets/images/logo.ico') }}" alt="small logo"></span>
                </span>
            </a>

            <!-- Sidebar Menu Toggle Button -->
            <button class="sidenav-toggle-button btn-icon rounded-circle btn btn-light">
                <i class="ti ti-menu-2 fs-22"></i>
            </button>

            <!-- Horizontal Menu Toggle Button -->
            <button class="topnav-toggle-button px-2" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                <i class="ti ti-menu-2 fs-22"></i>
            </button>

            <!-- Button Trigger Search Modal -->
            {{-- <div class="topbar-search text-muted d-none d-xl-flex gap-2 align-items-center" data-bs-toggle="modal" data-bs-target="#searchModal" type="button">
                <span class="me-2 fs-12 fw-medium">Search something..</span>
                <i class="ti ti-search fs-18 ms-auto"></i>
            </div> --}}

            <!-- Mega Menu Dropdown -->
            <div class="topbar-item d-none d-md-flex">

                 <!-- .dropdown-->
            </div> <!-- end topbar-item -->
        </div>
        <!-- Selector de tamaño de fuente -->

        <div class="d-flex align-items-center gap-2">
            <div class="topbar-item d-none d-sm-flex">
                <div class="dropdown">
                    <button class="topbar-link dropdown-toggle" data-bs-toggle="dropdown" type="button" aria-expanded="false" title="Tamaño de letra">
                    <i class="ti ti-letter-case fs-22"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-2" style="min-width: 240px">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light flex-fill text-nowrap" data-scale="0.80">A--</button>
                        <button type="button" class="btn btn-light flex-fill text-nowrap" data-scale="0.90">A-</button>
                        <button type="button" class="btn btn-light flex-fill text-nowrap" data-scale="1">A</button>
                        <button type="button" class="btn btn-light flex-fill text-nowrap" data-scale="1.10">A+</button>
                        <button type="button" class="btn btn-light flex-fill text-nowrap" data-scale="1.20">A++</button>
                    </div>
                    <small class="text-muted d-block mt-2 px-1">Se guarda para tus próximas visitas.</small>
                    </div>
                </div>
            </div>
            <!-- Light/Dark Mode Button -->
            <div class="topbar-item d-none d-sm-flex">
                <button class="topbar-link" id="light-dark-mode" type="button">
                    <i class="ti ti-moon fs-22"></i>
                </button>
            </div>

            <!-- User Dropdown -->
           <div class="topbar-item nav-user">
                <div class="dropdown">
                    <a class="topbar-link dropdown-toggle drop-arrow-none px-2" data-bs-toggle="dropdown" data-bs-offset="0,19" type="button" aria-haspopup="false" aria-expanded="false">
                        <img src="{{ asset('assets/images/avatar_generico.svg') }}" width="32" class="rounded-circle me-lg-2 d-flex" alt="user-image">
                        <span class="d-lg-flex flex-column gap-1 d-none">
                            <h5 class="my-0 fs-13 fw-semibold">{{ Auth::user()->name }}</h5>
                        </span>
                        <i class="ti ti-chevron-down d-none d-lg-block align-middle ms-2"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="dropdown-item active fw-semibold text-danger">
                            <i class="ti ti-logout me-1 fs-17 align-middle"></i><span class="align-middle">Cerrar Sesión</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<script>
  (function(){
    const KEY = 'ui.fontScale';

    function applyScale(scale) {
      document.documentElement.style.setProperty('--font-scale', scale);
      try { localStorage.setItem(KEY, String(scale)); } catch(e) {}
      // marcar botón activo
      document.querySelectorAll('[data-scale]').forEach(b => {
        b.classList.toggle('active', b.getAttribute('data-scale') === String(scale));
      });
    }

    document.addEventListener('DOMContentLoaded', function() {
      // aplicar escala guardada
      const saved = parseFloat(localStorage.getItem(KEY)) || 1;
      applyScale(saved);

      // clicks en opciones del menú
      document.querySelectorAll('[data-scale]').forEach(b => {
        b.addEventListener('click', function(ev){
          ev.preventDefault();
          const s = parseFloat(this.getAttribute('data-scale'));
          applyScale(s);
        });
      });
    });
  })();
</script>
