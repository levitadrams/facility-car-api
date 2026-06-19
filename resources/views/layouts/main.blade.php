<!DOCTYPE html>
<html lang="pt-BR" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../../assets/" data-template="vertical-menu-template">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Facility Car') }} | @stack('title')</title>

  <!-- Bloquear indexação de páginas internas -->
  <meta name="robots" content="noindex, nofollow" />
  <meta name="googlebot" content="noindex, nofollow" />

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon.png') }}" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com/">
  <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&amp;display=swap" rel="stylesheet">

  <!-- Icons -->
  <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/tabler-icons.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}" />

  <!-- Core CSS -->
  <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" class="template-customizer-core-css" />
  <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

  <!-- Vendors CSS -->
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />

  <link rel="stylesheet" href="{{ asset('assets/css/general.css') }}" />

  <!-- Helpers -->
  <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
  <script src="{{ asset('assets/js/config.js') }}"></script>

  <style>
    .swal2-container {
      z-index: 9999999999 !important;
    }

    .light-style .menu .app-brand.demo {
      height: 74px !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
    }
    .app-brand-link {
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      height: 60px !important;
      width: auto !important;
      max-width: 180px !important;
      margin: 0 auto !important;
    }

    .app-brand-logo.demo {
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      width: auto !important;
      height: 100% !important;
      max-width: 100% !important;
      max-height: 100% !important;
    }

    .app-brand-logo.demo img {
      width: auto !important;
      height: 100% !important;
      max-width: 180px !important;
      object-fit: contain !important;
    }

    /* Logo quando menu está colapsado */
    .layout-menu-collapsed .app-brand.demo {
      padding: 0 !important;
    }

    .layout-menu-collapsed .app-brand-link {
      max-width: 50px !important;
      height: 50px !important;
    }

    .layout-menu-collapsed .app-brand-logo.demo img {
      max-width: 50px !important;
      width: 50px !important;
      height: 35px !important;
      object-fit: cover !important;
    }

    /* Fix para avatares no menu superior */
    .navbar .avatar img {
      width: 100% !important;
      height: 100% !important;
      max-width: 40px !important;
      max-height: 40px !important;
      object-fit: cover !important;
    }

    .navbar .avatar {
      width: 40px !important;
      height: 40px !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      overflow: hidden !important;
    }

    /* Avatar dentro do dropdown */
    .dropdown-menu .avatar img {
      width: 100% !important;
      height: 100% !important;
      max-width: 100% !important;
      max-height: 100% !important;
      object-fit: cover !important;
    }

    .dropdown-menu .avatar {
      width: 40px !important;
      height: 40px !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      overflow: hidden !important;
    }

    /* Footer fixo dentro do content-wrapper */
    .custom-footer {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background-color: #f8f7fa;
      padding: 0.4rem 1.5rem;
      z-index: 10;
    }

    @media (min-width: 1200px) {
      .layout-menu-fixed .custom-footer {
        left: 16.25rem;
      }

      .layout-menu-fixed.layout-menu-collapsed .custom-footer {
        left: 5.25rem;
      }
    }

    .custom-footer .footer-container {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0.5rem 0;
    }

    @media (max-width: 768px) {
      .custom-footer .footer-container {
        flex-direction: column;
      }
    }
  </style>

  @stack('css')
</head>

<body>

  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
        <div class="app-brand demo">
          <a href="{{ route('dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
              <img src="{{ asset('assets/img/logo.png') }}" alt="Facility Car" onerror="this.style.display='none'"/>
            </span>
          </a>

          <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
          </a>
        </div>

        <div class="menu-inner-shadow"></div>

        @include('layouts.sidebar')

      </aside>

      <div class="layout-page">
        <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
          <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
              <i class="ti ti-menu-2 ti-sm"></i>
            </a>
          </div>

          <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
            <ul class="navbar-nav flex-row align-items-center ms-auto">

              <li class="nav-item dropdown-style-switcher dropdown me-2 me-xl-0">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                  <i class='ti ti-md'></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
                  <li>
                    <a class="dropdown-item" href="javascript:void(0);" data-theme="light">
                      <span class="align-middle"><i class='ti ti-sun me-2'></i>Light</span>
                    </a>
                  </li>

                  <li>
                    <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
                      <span class="align-middle"><i class='ti ti-moon me-2'></i>Dark</span>
                    </a>
                  </li>

                  <li>
                    <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
                      <span class="align-middle"><i class='ti ti-device-desktop me-2'></i>System</span>
                    </a>
                  </li>
                </ul>
              </li>

              <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                  <div class="avatar avatar-online">
                    <div class="avatar-initial rounded-circle bg-label-primary">
                      {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                  </div>
                </a>

                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <a class="dropdown-item" href="javascript:void(0)">
                      <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                          <div class="avatar avatar-online">
                            <div class="avatar-initial rounded-circle bg-label-primary">
                              {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                          </div>
                        </div>
                        <div class="flex-grow-1">
                          <span class="fw-medium d-block">{{ auth()->user()->name }}</span>
                          <small class="text-muted">Administrador</small>
                        </div>
                      </div>
                    </a>
                  </li>

                  <li>
                    <div class="dropdown-divider"></div>
                  </li>

                  <li>
                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                      <i class="ti ti-logout me-2 ti-sm"></i>
                      <span class="align-middle">Sair</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                      @csrf
                    </form>
                  </li>

                </ul>
              </li>
            </ul>
          </div>
        </nav>

        <div class="content-wrapper">
          <div class="container-xxl flex-grow-1 container-p-y">

            @yield('content')

          </div>

          <div class="custom-footer">
            <div class="container-xxl">
              <div class="footer-container">
                <div>
                  © <script> document.write(new Date().getFullYear()) </script>, Facility Car
                </div>
                <div class="d-none d-lg-inline-block">
                  <a href="" target="_blank" class="me-4">Documentação</a>
                  <a href="" target="_blank">Suporte</a>
                </div>
              </div>
            </div>
          </div>

          <div class="content-backdrop fade"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="layout-overlay layout-menu-toggle"></div>

  <div class="drag-target"></div>

  <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
  <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
  <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

  <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>

  <script src="{{ asset('assets/js/main.js') }}"></script>

  @if (Session::has('success'))
  <script>
    $(document).ready(function () {
        Swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: '<?= Session::get('success'); ?>',
            confirmButtonColor: '#3b82f6'
        });
    });
  </script>
  @endif

  @if (Session::has('error'))
  <script>
    $(document).ready(function () {
        Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: '<?= Session::get('error'); ?>',
            confirmButtonColor: '#ef4444'
        });
    });
  </script>
  @endif

  @if (Session::has('info'))
  <script>
    $(document).ready(function () {
        Swal.fire({
            icon: 'info',
            title: 'Info!',
            text: '<?= Session::get('info'); ?>',
            confirmButtonColor: '#3b82f6'
        });
    });
  </script>
  @endif

  @if (Session::has('warning'))
  <script>
    $(document).ready(function () {
        Swal.fire({
            icon: 'warning',
            title: 'Atenção!',
            text: '<?= Session::get('warning'); ?>',
            confirmButtonColor: '#f59e0b'
        });
    });
  </script>
  @endif

  @stack('js')
</body>
</html>
