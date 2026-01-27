    @php
$isEdit = !is_null($detail);
$submitText = $isEdit ? 'Сохранить' : 'Добавить деталь';
@endphp

<div class="detail-form-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-back-button">
            <a href="{{ $backRoute }}" class="btn-back">
                <svg viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.5">
                    <path fill-rule="evenodd" d="M12 19l-8-8 8-8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>

        <div class="header-content">
            <div class="header-title-row">
                <h1 class="header-title">{{ $title }}</h1>
            </div>
            <p class="header-code">{{ $isEdit ? $detail->scu : 'Новый элемент' }}</p>
        </div>

        @if($isEdit)
        <div class="header-actions">
            <a href="{{ route('details.show', $detail) }}" class="btn-secondary">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Просмотр
            </a>
        </div>
        @endif
    </div>

    <!-- Form Card -->
    <div class="card form-card">
        <form action="{{ $action }}" method="POST" class="detail-form">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <!-- Basic Information Section -->
            <div class="form-section">
                <div class="section-header">
                    <svg class="section-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                    </svg>
                    <h3 class="section-title">Основная информация</h3>
                </div>
                
                <div class="form-group">
                    <label for="name" class="form-label">Название *</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $isEdit ? $detail->name : '') }}" placeholder="Например: Профиль монтажный 2U" required>
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
                                <option value="{{ $category->id }}" {{ old('category_id', $isEdit ? $detail->category_id : '') == $category->id ? 'selected' : '' }}>
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
                                <option value="{{ $source->id }}" {{ old('source_id', $isEdit ? $detail->source_id : '') == $source->id ? 'selected' : '' }}>
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
                               value="{{ old('scu', $isEdit ? $detail->scu : 'шт') }}" placeholder="шт, кг, л и т.д." required>
                        @error('scu')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Описание</label>
                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                              placeholder="Подробное описание детали" rows="3">{{ old('description', $isEdit ? $detail->description : '') }}</textarea>
                    @error('description')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Dimensions Section -->
            <div class="form-section">
                <div class="section-header">
                    <svg class="section-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4z"/>
                    </svg>
                    <h3 class="section-title">Размеры и характеристики</h3>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="width" class="form-label">Ширина (мм) *</label>
                        <input type="number" id="width" name="width" class="form-control @error('width') is-invalid @enderror"
                               value="{{ old('width', $isEdit ? $detail->width : '') }}" step="0.01" placeholder="482" required>
                        @error('width')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="height" class="form-label">Высота (мм) *</label>
                        <input type="number" id="height" name="height" class="form-control @error('height') is-invalid @enderror"
                               value="{{ old('height', $isEdit ? $detail->height : '') }}" step="0.01" placeholder="89" required>
                        @error('height')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="depth" class="form-label">Глубина (мм) *</label>
                        <input type="number" id="depth" name="depth" class="form-control @error('depth') is-invalid @enderror"
                               value="{{ old('depth', $isEdit ? $detail->depth : '') }}" step="0.01" placeholder="2" required>
                        @error('depth')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="weight" class="form-label">Вес (кг) *</label>
                        <input type="number" id="weight" name="weight" class="form-control @error('weight') is-invalid @enderror"
                               value="{{ old('weight', $isEdit ? $detail->weight : '') }}" step="0.01" placeholder="1.2" required>
                        @error('weight')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="material" class="form-label">Материал</label>
                        <input type="text" id="material" name="material" class="form-control @error('material') is-invalid @enderror"
                               value="{{ old('material', $isEdit ? $detail->material : '') }}" placeholder="Сталь холоднокатаная">
                        @error('material')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Pricing Section -->
            <div class="form-section">
                <div class="section-header">
                    <svg class="section-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M8.5 5a2.5 2.5 0 015 0 .5.5 0 01-1 0 1.5 1.5 0 00-3 0c0 .893.231 1.734.645 2.467.41.726 1.062 1.447 1.855 2.09C11.926 9.812 13 10.855 13 12.5v1h-2v-1c0-.893-.231-1.734-.645-2.467-.41-.726-1.062-1.447-1.855-2.09C7.074 8.188 6 7.145 6 5.5 6 3.015 7.015 1 8.5 1s2.5 2.015 2.5 4.5a.5.5 0 01-1 0 1.5 1.5 0 00-1.5-1.5 1.5 1.5 0 00-1.5 1.5z"/>
                    </svg>
                    <h3 class="section-title">Стоимость</h3>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="price" class="form-label">Цена за шт (₽)</label>
                        <input type="number" id="price" name="price" class="form-control @error('price') is-invalid @enderror"
                               value="{{ old('price', $isEdit ? $detail->price : '') }}" step="0.01" placeholder="420">
                        @error('price')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="price_per_kg" class="form-label">Цена за кг (₽)</label>
                        <input type="number" id="price_per_kg" name="price_per_kg" class="form-control @error('price_per_kg') is-invalid @enderror"
                               value="{{ old('price_per_kg', $isEdit ? $detail->price_per_kg : '') }}" step="0.01" placeholder="350">
                        @error('price_per_kg')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                    </svg>
                    {{ $submitText }}
                </button>
                <a href="{{ $backRoute }}" class="btn-cancel">Отмена</a>
            </div>
        </form>
    </div>
</div>
