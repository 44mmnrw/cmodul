@extends('layout')

@section('title', 'Создать пользователя')

@section('content')
<div class="row">
    <div class="col-md-6">
        <h1>➕ Новый пользователь</h1>
        
        <form action="/users" method="POST" class="mt-4">
            @csrf
            
            <div class="mb-3">
                <label for="name" class="form-label">Имя <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Пароль <span class="text-danger">*</span></label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                       id="password" name="password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Подтверждение пароля <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
            </div>

            <div class="btn-group" role="group">
                <button type="submit" class="btn btn-success">Создать</button>
                <a href="/users" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
</div>
@endsection
