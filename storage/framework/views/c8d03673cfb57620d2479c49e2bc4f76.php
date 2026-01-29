<!-- Alert Modal Component -->
<div id="alertModal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closeAlertModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="alertTitle">Уведомление</h3>
            <button class="modal-close" onclick="closeAlertModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <p id="alertMessage">Сообщение</p>
        </div>
        <div class="modal-footer">
            <button class="btn-primary" onclick="closeAlertModal()">OK</button>
        </div>
    </div>
</div>

<script>
let alertCallback = null;

function showAlert(title, message, callback = null) {
    document.getElementById('alertTitle').textContent = title;
    document.getElementById('alertMessage').textContent = message;
    alertCallback = callback;
    document.getElementById('alertModal').style.display = 'flex';
}

function closeAlertModal() {
    document.getElementById('alertModal').style.display = 'none';
    if (alertCallback && typeof alertCallback === 'function') {
        alertCallback();
    }
    alertCallback = null;
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeAlertModal();
    }
});
</script>
<?php /**PATH C:\laragon\www\Cmodul\resources\views/components/alert-modal.blade.php ENDPATH**/ ?>