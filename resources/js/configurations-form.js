let componentCounter = 0;

function initConfigurationForm(initialCount = 0) {
    componentCounter = initialCount;
    console.log('Form initialized with counter:', componentCounter);
}

function addComponent() {
    console.log('addComponent called');
    
    const select = document.getElementById('component-select');
    const qty = document.getElementById('component-qty');
    const list = document.getElementById('components-list');
    
    if (!select) {
        alert('Ошибка: селект компонентов не найден');
        return;
    }
    
    if (!list) {
        alert('Ошибка: список компонентов не найден');
        return;
    }
    
    if (!select.value) {
        alert('Выберите компонент');
        return;
    }

    const option = select.options[select.selectedIndex];
    const componentId = select.value;
    const componentName = option.getAttribute('data-name');
    const componentScu = option.getAttribute('data-scu');
    const quantity = qty.value || 1;

    console.log('Adding component:', {componentId, componentName, componentScu, quantity});
    
    // Проверим не добавлен ли уже этот компонент
    if (document.querySelector(`[data-component-id="${componentId}"]`)) {
        alert('Этот компонент уже добавлен');
        return;
    }
    
    // Удаляем empty-state если он есть
    const emptyState = list.querySelector('.empty-state');
    if (emptyState) {
        console.log('Removing empty state');
        emptyState.remove();
    }

    const item = document.createElement('div');
    item.className = 'component-edit-item';
    item.setAttribute('data-component-id', componentId);
    item.innerHTML = `
        <div class="component-edit-info">
            <div class="component-edit-name">${componentName}</div>
            <div class="component-edit-code">${componentScu}</div>
        </div>
        <div class="component-edit-quantity">
            <input 
                type="hidden" 
                name="components[${componentCounter}][id]" 
                value="${componentId}"
            >
            <input 
                type="number" 
                name="components[${componentCounter}][quantity]" 
                class="component-qty-input" 
                value="${quantity}"
                min="1"
                required
            >
            <span class="component-qty-label">шт</span>
        </div>
        <button type="button" class="btn-remove-component" onclick="this.closest('.component-edit-item').remove()">
            <svg viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
            </svg>
        </button>
    `;

    list.appendChild(item);
    componentCounter++;
    
    console.log('Component added, counter now:', componentCounter);
    
    // Очищаем форму
    select.value = '';
    qty.value = '1';
    select.focus();
}

// Позволить добавить компонент нажатием Enter в поле количества
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing form...');
    
    const qtyInput = document.getElementById('component-qty');
    if (qtyInput) {
        qtyInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addComponent();
            }
        });
        console.log('Qty input listener added');
    }
    
    // Инициализируем счётчик
    const currentItemsCount = document.querySelectorAll('[data-component-id]').length;
    initConfigurationForm(currentItemsCount);
});

