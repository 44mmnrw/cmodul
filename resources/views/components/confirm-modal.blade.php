<!-- Confirmation Modal Component -->
<div id="confirmModal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closeConfirmModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="confirmTitle">Подтверждение</h3>
            <button class="modal-close" onclick="closeConfirmModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <p class="warning-text" id="confirmMessage">Вы уверены?</p>
            <p class="warning-subtext" id="confirmSubtext"></p>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeConfirmModal()">Отмена</button>
            <button class="btn-primary" id="confirmButton" onclick="window.__confirmCallback && window.__confirmCallback()">Подтвердить</button>
        </div>
    </div>
</div>

<script>
// Глобальная переменная для хранения callback
window.__confirmCallback = null;

function openConfirmModal(title, message, subtext = '', buttonText = 'Подтвердить', callback = null) {
    console.log('=== OPEN CONFIRM MODAL ===');
    console.log('Title:', title);
    console.log('Message:', message);
    console.log('Callback type:', typeof callback);
    
    document.getElementById('confirmTitle').textContent = title;
    document.getElementById('confirmMessage').textContent = message;
    document.getElementById('confirmSubtext').textContent = subtext;
    document.getElementById('confirmButton').textContent = buttonText;
    
    // Сохранить callback в глобальную переменную
    window.__confirmCallback = () => {
        console.log('Executing confirm callback');
        closeConfirmModal();
        if (callback && typeof callback === 'function') {
            console.log('Calling user callback');
            callback();
        }
    };
    
    console.log('Callback set, opening modal');
    document.getElementById('confirmModal').style.display = 'flex';
}

function closeConfirmModal() {
    console.log('Closing confirm modal');
    document.getElementById('confirmModal').style.display = 'none';
    window.__confirmCallback = null;
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && document.getElementById('confirmModal').style.display === 'flex') {
        closeConfirmModal();
    }
});
</script>
