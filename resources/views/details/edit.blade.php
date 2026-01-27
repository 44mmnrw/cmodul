@extends('layout')

@section('title', 'Редактировать ' . $detail->name)

@section('content')
<div class="detail-form-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <a href="{{ route('details.show', $detail) }}" class="back-link">
                <svg viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 111.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z"/>
                </svg>
                К деталям
            </a>
            <h1 class="page-title">Редактировать деталь: {{ $detail->name }}</h1>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card form-card">
        <form action="{{ route('details.update', $detail) }}" method="POST" class="detail-form">
            @csrf
            @method('PUT')

            <div class="form-section">
                <h3 class="section-title">Основная информация</h3>
                
                <div class="form-group">
                    <label for="name" class="form-label">Название *</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $detail->name) }}" placeholder="Например: Профиль монтажный 19" 2U"" required>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="category_id" class="form-label">Категория *</label>
                        <select id="category_id" name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                            <option value="">Выберите категорию</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $detail->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="source_id" class="form-label">Источник</label>
                        <select id="source_id" name="source_id" class="form-control @error('source_id') is-invalid @enderror">
                            <option value="">Не указан</option>
                            @foreach($sources as $source)
                                <option value="{{ $source->id }}" {{ old('source_id', $detail->source_id) == $source->id ? 'selected' : '' }}>
                                    {{ $source->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('source_id')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="scu" class="form-label">Единица измерения *</label>
                        <input type="text" id="scu" name="scu" class="form-control @error('scu') is-invalid @enderror"
                               value="{{ old('scu', $detail->scu) }}" placeholder="шт, кг, л и т.д." required>
                        @error('scu')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Описание</label>
                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                              placeholder="Подробное описание детали" rows="4">{{ old('description', $detail->description) }}</textarea>
                    @error('description')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title">Размеры и физические характеристики</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="width" class="form-label">Ширина (мм) *</label>
                        <input type="number" id="width" name="width" class="form-control @error('width') is-invalid @enderror"
                               value="{{ old('width', $detail->width) }}" step="0.01" placeholder="482" required>
                        @error('width')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="height" class="form-label">Высота (мм) *</label>
                        <input type="number" id="height" name="height" class="form-control @error('height') is-invalid @enderror"
                               value="{{ old('height', $detail->height) }}" step="0.01" placeholder="89" required>
                        @error('height')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="depth" class="form-label">Глубина (мм) *</label>
                        <input type="number" id="depth" name="depth" class="form-control @error('depth') is-invalid @enderror"
                               value="{{ old('depth', $detail->depth) }}" step="0.01" placeholder="2" required>
                        @error('depth')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="weight" class="form-label">Вес (кг) *</label>
                        <input type="number" id="weight" name="weight" class="form-control @error('weight') is-invalid @enderror"
                               value="{{ old('weight', $detail->weight) }}" step="0.01" placeholder="1.2" required>
                        @error('weight')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="material" class="form-label">Материал</label>
                        <input type="text" id="material" name="material" class="form-control @error('material') is-invalid @enderror"
                               value="{{ old('material', $detail->material) }}" placeholder="Сталь холоднокатаная">
                        @error('material')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title">Цены</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="price" class="form-label">Цена за шт (₽)</label>
                        <input type="number" id="price" name="price" class="form-control @error('price') is-invalid @enderror"
                               value="{{ old('price', $detail->price) }}" step="0.01" placeholder="420">
                        @error('price')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="price_per_kg" class="form-label">Цена за кг (₽)</label>
                        <input type="number" id="price_per_kg" name="price_per_kg" class="form-control @error('price_per_kg') is-invalid @enderror"
                               value="{{ old('price_per_kg', $detail->price_per_kg) }}" step="0.01" placeholder="350">
                        @error('price_per_kg')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Сохранить изменения</button>
                <a href="{{ route('details.show', $detail) }}" class="btn-cancel">Отмена</a>
            </div>
        </form>
    </div>
</div>

<style>
    /* Form Page */
    .detail-form-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
        max-width: 800px;
    }

    /* Header */
    .page-header {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #3b82f6;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s ease;
        width: fit-content;
    }

    .back-link:hover {
        color: #2563eb;
    }

    .back-link svg {
        width: 20px;
        height: 20px;
    }

    .page-title {
        font-size: 32px;
        font-weight: bold;
        color: #101828;
        margin: 0;
        line-height: 40px;
    }

    /* Card */
    .card {
        background: white;
        border-radius: 10px;
        box-shadow: 0px 1px 3px rgba(0,0,0,0.1), 0px 1px 2px rgba(0,0,0,0.1);
    }

    .form-card {
        padding: 32px;
    }

    /* Form */
    .detail-form {
        display: flex;
        flex-direction: column;
        gap: 32px;
    }

    /* Form Sections */
    .form-section {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #101828;
        margin: 0 0 8px 0;
        line-height: 28px;
    }

    /* Form Groups */
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .form-row.full {
        grid-template-columns: 1fr;
    }

    /* Form Labels */
    .form-label {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        line-height: 20px;
    }

    /* Form Controls */
    .form-control {
        padding: 10px 16px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        color: #101828;
        background: white;
        transition: all 0.2s ease;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    }

    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-control::placeholder {
        color: #9ca3af;
    }

    .form-control.is-invalid {
        border-color: #ef4444;
    }

    .form-control.is-invalid:focus {
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 120px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    }

    /* Error Message */
    .error-message {
        font-size: 12px;
        color: #ef4444;
        line-height: 16px;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 16px;
    }

    .btn-submit,
    .btn-cancel {
        padding: 10px 24px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        text-align: center;
        line-height: 20px;
    }

    .btn-submit {
        background: #3b82f6;
        color: white;
        flex: 1;
    }

    .btn-submit:hover {
        background: #2563eb;
        box-shadow: 0px 4px 12px rgba(59, 130, 246, 0.3);
    }

    .btn-cancel {
        background: #f3f4f6;
        color: #374151;
        flex: 1;
    }

    .btn-cancel:hover {
        background: #e5e7eb;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .detail-form-page {
            max-width: 100%;
        }

        .form-card {
            padding: 24px;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .page-title {
            font-size: 24px;
            line-height: 32px;
        }

        .btn-submit,
        .btn-cancel {
            padding: 12px 16px;
        }
    }
</style>
@endsection
