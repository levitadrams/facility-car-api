@extends('layouts.main')

@section('title', 'Dashboard - Facility Car')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Dashboard</h5>
            </div>
            <div class="card-body">
                <p class="card-text">Bem-vindo, <strong>{{ auth()->user()->name }}</strong>!</p>
                <p class="text-muted">Esta área será expandida com funcionalidades de gerenciamento da frota.</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar bg-label-primary rounded me-3">
                        <span class="avatar-initial rounded-circle bg-label-primary">
                            <i class="ti ti-car ti-lg"></i>
                        </span>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Veículos</h6>
                        <h4 class="mb-0">0</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar bg-label-success rounded me-3">
                        <span class="avatar-initial rounded-circle bg-label-success">
                            <i class="ti ti-map-pin ti-lg"></i>
                        </span>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Destinos</h6>
                        <h4 class="mb-0">0</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar bg-label-warning rounded me-3">
                        <span class="avatar-initial rounded-circle bg-label-warning">
                            <i class="ti ti-route ti-lg"></i>
                        </span>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Rotas</h6>
                        <h4 class="mb-0">0</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar bg-label-danger rounded me-3">
                        <span class="avatar-initial rounded-circle bg-label-danger">
                            <i class="ti ti-wrench ti-lg"></i>
                        </span>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Manutenções</h6>
                        <h4 class="mb-0">0</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
