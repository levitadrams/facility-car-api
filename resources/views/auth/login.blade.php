@extends('layouts.app')

@section('title', 'Login - Facility Car')

@section('content')
<div class="auth-form-header">
    <h4 class="auth-form-title">Bem-vindo de volta!</h4>
    <p class="auth-form-description">Por favor, entre com suas credenciais para acessar o sistema.</p>
</div>

<form class="mb-3" method="POST" action="{{ route('login') }}">
    @csrf

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Erro!</h4>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-3">
        <label for="email" class="form-label">E-mail</label>
        <div class="input-group input-group-merge">
            <span class="input-group-text">
                <i class="ti ti-mail"></i>
            </span>
            <input
                type="email"
                class="form-control"
                id="email"
                name="email"
                placeholder="seu@email.com"
                value="{{ old('email') }}"
                required
                autofocus
            />
        </div>
    </div>

    <div class="mb-3">
        <div class="d-flex justify-content-between">
            <label class="form-label" for="password">Senha</label>
        </div>
        <div class="input-group input-group-merge password-toggle">
            <span class="input-group-text">
                <i class="ti ti-lock"></i>
            </span>
            <input
                type="password"
                class="form-control"
                id="password"
                name="password"
                placeholder="••••••••"
                required
            />
            <span class="input-group-text cursor-pointer">
                <i class="ti ti-eye-off"></i>
            </span>
        </div>
    </div>

    <div class="mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="remember" name="remember">
            <label class="form-check-label" for="remember">
                Lembrar-me
            </label>
        </div>
    </div>

    <button type="submit" class="btn btn-primary d-grid w-100">
        Entrar
    </button>
</form>

@endsection
