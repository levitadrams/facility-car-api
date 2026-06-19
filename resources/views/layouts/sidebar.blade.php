<ul class="menu-inner py-1">

    <li class="menu-item">
        <a href="{{ route('dashboard') }}" class="menu-link">
            <i class="menu-icon tf-icons ti ti-home"></i>
            <div data-i18n="Painel">Painel</div>
        </a>
    </li>

    <!-- MENU ADMIN -->
    @if(auth()->user()->is_admin ?? false)
        <li class="menu-item">
            <a href="#" class="menu-link">
                <i class="menu-icon tf-icons ti ti-users"></i>
                <div data-i18n="Usuários">Usuários</div>
            </a>
        </li>
    @endif

    <!-- MENU FROTA -->
    <li class="menu-item @if(request()->is('veiculos*') || request()->is('destinos*')) open @endif">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons ti ti-car"></i>
            <div data-i18n="Frota">Frota</div>
        </a>

        <ul class="menu-sub">
            <li class="menu-item">
                <a href="#" class="menu-link">
                    <div data-i18n="Veículos">Veículos</div>
                </a>
            </li>

            <li class="menu-item">
                <a href="#" class="menu-link">
                    <div data-i18n="Destinos">Destinos</div>
                </a>
            </li>

            <li class="menu-item">
                <a href="#" class="menu-link">
                    <div data-i18n="Rotas">Rotas</div>
                </a>
            </li>
        </ul>
    </li>

    <!-- MENU MANUTENÇÃO -->
    <li class="menu-item @if(request()->is('manutencao*')) open @endif">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons ti ti-wrench"></i>
            <div data-i18n="Manutenção">Manutenção</div>
        </a>

        <ul class="menu-sub">
            <li class="menu-item">
                <a href="#" class="menu-link">
                    <div data-i18n="Ordens de Serviço">Ordens de Serviço</div>
                </a>
            </li>

            <li class="menu-item">
                <a href="#" class="menu-link">
                    <div data-i18n="Histórico">Histórico</div>
                </a>
            </li>
        </ul>
    </li>

    <!-- MENU RELATÓRIOS -->
    <li class="menu-item @if(request()->is('relatorios*')) open @endif">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons ti ti-chart-bar"></i>
            <div data-i18n="Relatórios">Relatórios</div>
        </a>

        <ul class="menu-sub">
            <li class="menu-item">
                <a href="#" class="menu-link">
                    <div data-i18n="Uso de Veículos">Uso de Veículos</div>
                </a>
            </li>

            <li class="menu-item">
                <a href="#" class="menu-link">
                    <div data-i18n="Custos de Manutenção">Custos de Manutenção</div>
                </a>
            </li>

            <li class="menu-item">
                <a href="#" class="menu-link">
                    <div data-i18n="Eficiência da Frota">Eficiência da Frota</div>
                </a>
            </li>
        </ul>
    </li>

    <!-- MENU CONFIGURAÇÕES -->
    <li class="menu-item @if(request()->is('configuracoes*')) open @endif">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons ti ti-settings"></i>
            <div data-i18n="Configurações">Configurações</div>
        </a>

        <ul class="menu-sub">
            <li class="menu-item">
                <a href="#" class="menu-link">
                    <div data-i18n="Perfil">Perfil</div>
                </a>
            </li>

            <li class="menu-item">
                <a href="#" class="menu-link">
                    <div data-i18n="Empresa">Empresa</div>
                </a>
            </li>
        </ul>
    </li>

    <li class="menu-item">
        <a href="{{ route('logout') }}" class="menu-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="menu-icon tf-icons ti ti-door-exit"></i>
            <div data-i18n="Sair">Sair</div>
        </a>
    </li>
</ul>
