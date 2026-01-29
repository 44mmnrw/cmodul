@extends('layout')

@section('title', $status ? 'Редактировать статус' : 'Добавить статус')

@section('content')
<div class="status-form-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">{{ $status ? 'Редактировать статус' : 'Добавить новый статус' }}</h1>
            @if($status)
                <p class="page-subtitle">{{ $status->code }}</p>
            @endif
        </div>
        <div class="header-actions">
            <a href="{{ route('production-order-statuses.index') }}" class="btn-secondary">
                <svg class="btn-icon">
                    <use xlink:href="#icon-arrow-left"></use>
                </svg>
                <span>Назад</span>
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form action="{{ $status ? route('production-order-statuses.update', $status) : route('production-order-statuses.store') }}" method="POST" class="status-form">
            @csrf
            @if($status)
                @method('PUT')
            @endif

            <div class="form-group">
                <label for="name" class="form-label">Название <span class="required">*</span></label>
                <input type="text" name="name" id="name" class="form-input @error('name') is-invalid @enderror" 
                    value="{{ old('name', $status?->name) }}" 
                    placeholder="Ожидает, В производстве, Готов..."
                    required>
                @error('name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="color" class="form-label">Цвет <span class="required">*</span></label>
                <div class="color-input-group">
                    <input type="color" name="color" id="color" class="form-input color-input @error('color') is-invalid @enderror" 
                        value="{{ old('color', $status?->color ?? '#6b7280') }}" required>
                    <input type="text" class="form-input color-code-input @error('color') is-invalid @enderror" 
                        id="color-code" value="{{ old('color', $status?->color ?? '#6b7280') }}" readonly>
                </div>
                @error('color')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Описание</label>
                <textarea name="description" id="description" class="form-textarea @error('description') is-invalid @enderror" 
                    placeholder="Описание статуса и его использования..."
                    rows="4">{{ old('description', $status?->description) }}</textarea>
                @error('description')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="sort_order" class="form-label">Порядок сортировки <span class="required">*</span></label>
                <input type="number" name="sort_order" id="sort_order" class="form-input @error('sort_order') is-invalid @enderror" 
                    value="{{ old('sort_order', $status?->sort_order ?? 0) }}" required>
                @error('sort_order')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('production-order-statuses.index') }}" class="btn-secondary">Отмена</a>
                <button type="submit" class="btn-primary">
                    {{ $status ? 'Сохранить изменения' : 'Добавить статус' }}
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.status-form-page {
    max-width: 600px;
    margin: 0 auto;
    padding: 24px;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 32px;
    gap: 24px;
}

.page-header-content {
    flex: 1;
}

.page-title {
    font-size: 24px;
    font-weight: 600;
    color: #101828;
    margin: 0 0 8px 0;
}

.page-subtitle {
    font-size: 14px;
    color: #6b7280;
    margin: 0;
}

.header-actions {
    display: flex;
    gap: 12px;
}

.btn-secondary, .btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    border: none;
    transition: all 0.2s ease;
}

.btn-secondary {
    background: white;
    border: 1px solid #d1d5dc;
    color: #364153;
}

.btn-secondary:hover {
    background: #f9fafb;
}

.btn-primary {
    background: #4f39f6;
    color: white;
}

.btn-primary:hover {
    background: #4330d9;
}

.btn-icon {
    width: 16px;
    height: 16px;
}

.form-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 28px;
}

.form-group {
    margin-bottom: 24px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #364153;
}

.required {
    color: #dc2626;
}

.form-input, .form-textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #d1d5dc;
    border-radius: 8px;
    font-size: 14px;
    color: #101828;
    background: white;
    font-family: inherit;
    transition: all 0.2s ease;
    box-sizing: border-box;
}

.form-input:focus, .form-textarea:focus {
    outline: none;
    border-color: #4f39f6;
    box-shadow: 0 0 0 3px rgba(79, 57, 246, 0.1);
}

.form-input.is-invalid, .form-textarea.is-invalid {
    border-color: #dc2626;
}

.form-input.is-invalid:focus, .form-textarea.is-invalid:focus {
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
}

.form-textarea {
    resize: vertical;
}

.form-error {
    display: block;
    margin-top: 6px;
    font-size: 13px;
    color: #dc2626;
}

.color-input-group {
    display: flex;
    gap: 12px;
    align-items: center;
}

.color-input {
    width: 60px;
    height: 42px;
    padding: 2px;
    cursor: pointer;
}

.color-code-input {
    flex: 1;
    font-family: monospace;
    font-size: 13px;
}

.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 32px;
}

.form-actions .btn-secondary,
.form-actions .btn-primary {
    flex: 1;
    justify-content: center;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorInput = document.getElementById('color');
    const colorCodeInput = document.getElementById('color-code');
    
    if (colorInput && colorCodeInput) {
        colorInput.addEventListener('change', function() {
            colorCodeInput.value = this.value;
        });
        
        colorCodeInput.addEventListener('change', function() {
            if (/^#[0-9A-F]{6}$/i.test(this.value)) {
                colorInput.value = this.value;
            }
        });
    }
});
</script>
@endsection
