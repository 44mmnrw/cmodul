@extends('layout')

@section('title', 'Добавить место')

@section('content')
<div class="form-page">
    <div class="form-header">
        <a href="{{ route('places.index') }}" class="back-button">
            <svg viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"/>
            </svg>
        </a>
        <h1 class="form-title">Добавить новое место</h1>
    </div>

    <div class="form-container">
        <form action="{{ route('places.store') }}" method="POST" class="form">
            @csrf

            <div class="form-group">
                <label for="place_id" class="form-label">Код места *</label>
                <input type="text" id="place_id" name="place_id" class="form-input @error('place_id') is-invalid @enderror" required value="{{ old('place_id') }}">
                @error('place_id')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="name" class="form-label">Название *</label>
                <input type="text" id="name" name="name" class="form-input @error('name') is-invalid @enderror" required value="{{ old('name') }}">
                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('places.index') }}" class="btn-secondary">Отмена</a>
                <button type="submit" class="btn-primary">Создать место</button>
            </div>
        </form>
    </div>
</div>

<style>
    .form-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
        max-width: 600px;
    }

    .form-header {
        display: flex;
        align-items: center;
        gap: 16px;
        padding-bottom: 16px;
        border-bottom: 1px solid #e5e7eb;
    }

    .back-button {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        color: #6a7282;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }

    .back-button:hover {
        border-color: #d1d5db;
        background-color: #f9fafb;
    }

    .form-title {
        font-size: 24px;
        font-weight: 700;
        color: #101828;
        margin: 0;
    }

    .form-container {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 24px;
    }

    .form {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-label {
        font-size: 14px;
        font-weight: 500;
        color: #101828;
    }

    .form-input {
        padding: 10px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.2s;
    }

    .form-input:focus {
        outline: none;
        border-color: #155dfc;
        box-shadow: 0 0 0 3px rgba(21, 93, 252, 0.1);
    }

    .form-input.is-invalid {
        border-color: #ef4444;
    }

    .error-message {
        font-size: 12px;
        color: #ef4444;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .btn-primary, .btn-secondary {
        padding: 10px 16px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-primary {
        background: #155dfc;
        color: white;
    }

    .btn-primary:hover {
        background: #0d47bb;
    }

    .btn-secondary {
        background: white;
        color: #101828;
        border: 1px solid #e5e7eb;
    }

    .btn-secondary:hover {
        background-color: #f9fafb;
        border-color: #d1d5db;
    }
</style>
@endsection
