<!-- Modal: Production Intake -->
<div id="productionIntakeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl max-h-96 overflow-hidden flex flex-col">
        <!-- Header -->
        <div class="bg-white border-b border-gray-200 px-6 py-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-blue-100 rounded-lg p-2">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm0 2h12v10H4V5z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Приход компонентов из производства</h2>
                    <p class="text-sm text-gray-600">Заполните таблицу для регистрации прихода</p>
                </div>
            </div>
            <button onclick="document.getElementById('productionIntakeModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Table -->
        <div class="overflow-auto flex-1">
            <form id="productionIntakeForm">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 border-r border-gray-200">Компонент <span class="text-red-500">*</span></th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 border-r border-gray-200">Дата прихода <span class="text-red-500">*</span></th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 border-r border-gray-200">Количество <span class="text-red-500">*</span></th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 border-r border-gray-200">Примечания</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700 w-12"></th>
                        </tr>
                    </thead>
                    <tbody id="intakeTableBody">
                        <!-- Rows will be added here -->
                    </tbody>
                </table>
            </form>
        </div>

        <!-- Add Row Button -->
        <div class="bg-white border-t border-gray-200 px-6 py-3">
            <button onclick="addIntakeRow()" class="flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"/>
                </svg>
                Добавить строку
            </button>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex items-center justify-between">
            <p class="text-sm text-gray-600">Заполнено строк: <span id="filledRowsCount">0</span> из 3</p>
            <div class="flex gap-3">
                <button onclick="document.getElementById('productionIntakeModal').classList.add('hidden')" 
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                    Отмена
                </button>
                <button onclick="saveProductionIntake()" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1 4.5 4.5 0 11-4.514 6.949z"/>
                    </svg>
                    Сохранить приход
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Get places from server
const placesData = @json($detail->places ?? []);

function addIntakeRow() {
    const tbody = document.getElementById('intakeTableBody');
    const rowCount = tbody.querySelectorAll('tr').length;
    
    if (rowCount >= 3) {
        alert('Максимум 3 строки');
        return;
    }

    const row = document.createElement('tr');
    row.className = 'border-b border-gray-200 hover:bg-gray-50';
    row.innerHTML = `
        <td class="px-4 py-3 border-r border-gray-200">
            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:border-blue-500" required>
                <option value="">Выберите место...</option>
                ${placesData.map(p => `<option value="${p.id}">${p.name} (${p.place_id})</option>`).join('')}
            </select>
        </td>
        <td class="px-4 py-3 border-r border-gray-200">
            <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:border-blue-500" required>
        </td>
        <td class="px-4 py-3 border-r border-gray-200">
            <input type="number" min="0" value="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:border-blue-500" required>
        </td>
        <td class="px-4 py-3 border-r border-gray-200">
            <input type="text" placeholder="Дополнительная информация..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:border-blue-500">
        </td>
        <td class="px-4 py-3 text-center">
            <button type="button" onclick="this.closest('tr').remove(); updateRowCount()" class="text-red-500 hover:text-red-700 transition">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
                </svg>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    updateRowCount();
}

function updateRowCount() {
    const tbody = document.getElementById('intakeTableBody');
    const rowCount = tbody.querySelectorAll('tr').length;
    document.getElementById('filledRowsCount').textContent = rowCount;
}

function saveProductionIntake() {
    const form = document.getElementById('productionIntakeForm');
    const rows = form.querySelectorAll('tbody tr');
    
    if (rows.length === 0) {
        alert('Добавьте хотя бы одну строку');
        return;
    }

    const data = Array.from(rows).map(row => {
        const cells = row.querySelectorAll('input, select');
        return {
            place_id: cells[0].value,
            received_date: cells[1].value,
            quantity: parseInt(cells[2].value),
            notes: cells[3].value || null
        };
    });

    console.log('Saving:', data);
    // Here you would send data to server
    // fetch('/place-stock-movements', { method: 'POST', body: JSON.stringify(data) })
    
    alert('Приход зарегистрирован!');
    document.getElementById('productionIntakeModal').classList.add('hidden');
}

// Initialize with one empty row
addIntakeRow();
</script>
