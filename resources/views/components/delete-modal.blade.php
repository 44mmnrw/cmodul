<!-- Delete Confirmation Modal Component -->
<div id="deleteModal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closeDeleteModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Удалить</h3>
            <button class="modal-close" onclick="closeDeleteModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <p class="warning-text">{{ $warningText ?? 'Вы уверены, что хотите удалить этот элемент?' }}</p>
            <p class="warning-subtext">{{ $warningSubtext ?? 'Это действие невозможно отменить.' }}</p>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeDeleteModal()">Отмена</button>
            <form id="deleteForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">Удалить</button>
            </form>
        </div>
    </div>
</div>

<script>
function openDeleteModal(event, deleteUrl) {
    event.stopPropagation();
    const modal = document.getElementById('deleteModal');
    const form = document.getElementById('deleteForm');
    form.action = deleteUrl;
    modal.style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
});
</script>
