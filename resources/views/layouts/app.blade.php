<!DOCTYPE html>
<html lang="pt-BR" class="light-style layout-wide customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="../../assets/" data-template="vertical-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Facility Car') }} | @stack('title')</title>

    <!-- Bloquear indexação de páginas de autenticação -->
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
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/tabler-icons.css') }}"/>

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" class="template-customizer-core-css"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}" class="template-customizer-theme-css"/>
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}"/>

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css') }}"/>

    <!-- Vendor -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/form-validation.css') }}"/>

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>

    <style>
        /* Custom Auth Layout - Facility Car */
        .auth-wrapper-facility {
            min-height: 100vh;
            display: flex;
            align-items: stretch;
        }

        .auth-side-brand {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 40%, #3b82f6 100%);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            color: white;
            min-height: 100vh;
        }

        /* Padrão geométrico de fundo */
        .auth-side-brand::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 30px 30px;
            transform: rotate(45deg);
        }

        .auth-side-brand::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -15%;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }

        .auth-brand-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 500px;
        }

        .auth-brand-title {
            font-size: 2.125rem;
            font-weight: 700;
            margin-bottom: 1.25rem;
            line-height: 1.25;
            color: #ffffff;
            text-shadow: 0 2px 12px rgba(0,0,0,0.25);
            letter-spacing: -0.02em;
        }

        .auth-brand-subtitle {
            font-size: 1.05rem;
            line-height: 1.6;
            margin-bottom: 2rem;
            color: rgba(255,255,255,0.92);
            font-weight: 400;
        }

        .auth-brand-steps {
            margin-top: 3rem;
            display: grid;
            gap: 1.5rem;
        }

        .auth-step-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            background: rgba(255,255,255,0.12);
            padding: 1.25rem 1.5rem;
            border-radius: 12px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.15);
            transition: all 0.3s ease;
        }

        .auth-step-item:hover {
            background: rgba(255,255,255,0.18);
            border-color: rgba(255,255,255,0.25);
            transform: translateX(8px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }

        .auth-step-number {
            background: rgba(255,255,255,0.95);
            color: #1e3a8a;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.375rem;
            font-weight: 700;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        .auth-step-content {
            flex: 1;
        }

        .auth-step-title {
            font-weight: 600;
            font-size: 1.0625rem;
            margin-bottom: 0.375rem;
            color: #ffffff;
            letter-spacing: -0.01em;
        }

        .auth-step-description {
            font-size: 0.9375rem;
            color: rgba(255,255,255,0.88);
            line-height: 1.5;
        }

        .auth-side-form {
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            min-height: 100vh;
        }

        .auth-form-container {
            width: 100%;
            max-width: 420px;
        }

        .auth-form-logo {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .auth-form-logo img {
            width: 160px;
            height: auto;
        }

        .auth-form-header {
            margin-bottom: 2rem;
        }

        .auth-form-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .auth-form-description {
            color: #6c757d;
            font-size: 0.94rem;
            line-height: 1.5;
        }

        /* Inputs com ícones */
        .input-group-text {
            background-color: #f8f9fa;
            border-right: 0;
            color: #3b82f6;
        }

        .input-group .form-control {
            border-left: 0;
        }

        .input-group .form-control:focus {
            border-left: 0;
        }

        .input-group:focus-within .input-group-text {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .auth-side-brand {
                display: none;
            }
        }

        @media (min-width: 992px) {
            .auth-form-logo {
                display: block;
            }
        }

        /* Form Elements */
        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.15);
        }

        .btn-primary {
            background-color: #3b82f6;
            border-color: #3b82f6;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: #2563eb;
            border-color: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .auth-footer-link {
            color: #3b82f6;
            text-decoration: none;
        }

        .auth-footer-link:hover {
            color: #1e3a8a;
            text-decoration: underline;
        }
    </style>

    @stack('css')
</head>

<body>

<div class="auth-wrapper-facility">
    <div class="row g-0 w-100">
        <!-- Lado Esquerdo - Branding -->
        <div class="col-lg-7 d-none d-lg-block">
            <div class="auth-side-brand">
                <div class="auth-brand-content">
                    <h1 class="auth-brand-title">
                        Gestão Inteligente de<br>Rotas e Destinos
                    </h1>

                    <p class="auth-brand-subtitle">
                        Otimize sua frota em 3 passos simples:
                    </p>

                    <div class="auth-brand-steps">
                        <div class="auth-step-item">
                            <div class="auth-step-number">1</div>
                            <div class="auth-step-content">
                                <div class="auth-step-title">Configure em minutos</div>
                                <div class="auth-step-description">Cadastre seus veículos e comece a usar imediatamente</div>
                            </div>
                        </div>
                        <div class="auth-step-item">
                            <div class="auth-step-number">2</div>
                            <div class="auth-step-content">
                                <div class="auth-step-title">Registre destinos</div>
                                <div class="auth-step-description">Adicione rotas e pontos de interesse no mapa</div>
                            </div>
                        </div>
                        <div class="auth-step-item">
                            <div class="auth-step-number">3</div>
                            <div class="auth-step-content">
                                <div class="auth-step-title">Otimize trajetos</div>
                                <div class="auth-step-description">Calcule distâncias e tempos automaticamente</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lado Direito - Formulário -->
        <div class="col-12 col-lg-5">
            <div class="auth-side-form">
                <div class="auth-form-container">
                    <!-- Logo sempre visível -->
                    <div class="auth-form-logo">
                        <img src="{{ asset('assets/img/logo.png') }}" alt="Facility Car" onerror="this.style.display='none'">
                    </div>

                    @yield('content')
                </div>
            </div>
        </div>
    </div>
</div>

<!-- / Content -->

  <!-- Core JS -->
  <!-- build:js assets/vendor/js/core.js -->
  <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
  <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/i18n/i18n.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
  <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
  <!-- endbuild -->

  <!-- Vendors JS -->
  <script src="{{ asset('assets/vendor/libs/@form-validation/popular.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>

  <!-- Main JS -->
  <script src="{{ asset('assets/js/main.js') }}"></script>
  <!-- Page JS -->
  <script src="{{ asset('assets/js/pages-auth.js') }}"></script>

  @stack('js')
</body>
</html>
