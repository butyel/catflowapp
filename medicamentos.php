<?php
require_once __DIR__ . '/src/auth.php';
require_login();

$gato_id = isset($_GET['gato_id']) ? (int)$_GET['gato_id'] : 0;
$page_title = "Medicamentos";
require_once __DIR__ . '/src/header.php';
?>

<!-- Tabs -->
<div class="flex bg-white rounded-xl shadow-sm mb-6 -mt-4 p-1 overflow-x-auto">
    <button onclick="window.location.href='saude.php<?php echo $gato_id ? '?gato_id=' . $gato_id : ''; ?>'" class="flex-1 py-2 px-4 text-sm font-medium text-gray-500 hover:bg-gray-50 rounded-lg whitespace-nowrap transition-colors">Histórico & Vacinas</button>
    <button class="flex-1 py-2 px-4 text-sm font-bold bg-brand-50 text-brand-600 rounded-lg whitespace-nowrap">Check-in de Doses</button>
</div>

<!-- Add Button -->
<div class="flex justify-end mb-6">
    <button onclick="toggleModal('medModal')" class="bg-brand-500 hover:bg-brand-600 text-white font-semibold py-2 px-5 rounded-xl flex items-center shadow-md">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        Novo Tratamento
    </button>
</div>

<!-- List of Active Medications -->
<div id="med-list" class="space-y-4 mb-8">
    <div class="text-center py-10">Buscando tratamentos...</div>
</div>

<!-- Modal: Novo Tratamento -->
<div id="medModal" class="fixed inset-0 z-[60] bg-black/50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto" id="medModalContent">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-5">Novo Tratamento</h2>
            <form id="medForm" onsubmit="saveMed(event)" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gato *</label>
                    <select id="modal_gato_id" name="gato_id" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none bg-white">
                        <!-- Populated via JS -->
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Medicamento *</label>
                    <input type="text" id="nome_medicamento" name="nome_medicamento" placeholder="Ex: Meloxicam" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dosagem *</label>
                        <input type="text" id="dosagem" name="dosagem" required placeholder="Ex: 1/2 comprimido" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Duração (Dias) *</label>
                        <input type="number" id="duracao_dias" name="duracao_dias" required placeholder="Ex: 5" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Horário Sugerido *</label>
                    <input type="time" id="horario" name="horario" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none text-gray-500">
                </div>
                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="toggleModal('medModal')" class="flex-1 bg-gray-100 text-gray-700 font-semibold py-3 rounded-xl transition-colors">Cancelar</button>
                    <button type="submit" id="saveBtn" class="flex-1 bg-brand-500 text-white font-semibold py-3 rounded-xl shadow-md transition-colors">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const currentGatoId = <?php echo $gato_id; ?>;

function toggleModal(modalID) {
    const el = document.getElementById(modalID);
    if (el.classList.contains('hidden')) {
        document.getElementById('medForm').reset();
        if (currentGatoId) document.getElementById('modal_gato_id').value = currentGatoId;
        el.classList.remove('hidden'); el.classList.add('flex');
    } else {
        el.classList.add('hidden'); el.classList.remove('flex');
    }
}

async function loadDropdownGatos() {
    try {
        const res = await fetch('api/list_gatos.php');
        const json = await res.json();
        if (json.success) {
            const select = document.getElementById('modal_gato_id');
            json.data.forEach(g => select.add(new Option(g.nome, g.id)));
            if (currentGatoId) select.value = currentGatoId;
        }
    } catch(e) {}
}

async function loadMedicamentos() {
    try {
        const url = currentGatoId ? `api/list_medicamentos.php?gato_id=${currentGatoId}` : 'api/list_medicamentos.php';
        const res = await fetch(url);
        const json = await res.json();
        const list = document.getElementById('med-list');
        list.innerHTML = '';

        if (json.success && json.data.length > 0) {
            json.data.forEach(med => {
                const card = document.createElement('div');
                card.className = "bg-white p-5 rounded-3xl shadow-sm border border-gray-100 mb-6";
                
                // Calculate schedule
                const startDate = new Date(med.created_at);
                const duracao = parseInt(med.duracao_dias);
                const historyDates = med.historico.map(h => new Date(h.data_aplicacao).toDateString());
                
                let scheduleHtml = '';
                for (let i = 0; i < duracao; i++) {
                    const dayDate = new Date(startDate);
                    dayDate.setDate(startDate.getDate() + i);
                    const isTaken = historyDates.includes(dayDate.toDateString());
                    const isToday = dayDate.toDateString() === new Date().toDateString();
                    
                    scheduleHtml += `
                        <div class="flex flex-col items-center gap-1 group">
                            <span class="text-[8px] font-black text-gray-400 uppercase">D${i+1}</span>
                            <div class="w-7 h-7 rounded-lg border-2 flex items-center justify-center transition-all ${isTaken ? 'bg-green-500 border-green-500 text-white shadow-sm' : (isToday ? 'border-orange-400 bg-orange-50 text-orange-600 animate-pulse' : 'border-gray-100 bg-gray-50 text-gray-300')}">
                                ${isTaken ? '✓' : (isToday ? '⌛' : i+1)}
                            </div>
                            <span class="text-[7px] text-gray-300 group-hover:text-gray-500">${dayDate.toLocaleDateString('pt-BR', {day:'2-digit', month:'2-digit'})}</span>
                        </div>
                    `;
                }

                card.innerHTML = `
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h3 class="text-lg font-black text-gray-800 leading-tight">${med.nome_medicamento}</h3>
                                    <p class="text-[10px] text-brand-600 font-black uppercase tracking-widest">${med.gato_nome} • ${med.dosagem} • ${med.horario}h</p>
                                </div>
                                <button onclick="deleteMed(${med.id})" class="text-gray-300 hover:text-red-500 p-2 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                            
                            <!-- Dose Tracker Grid -->
                            <div class="my-5">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                    <svg class="w-3 h-3 text-brand-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"></path></svg>
                                    Cronograma de Tratamento (${duracao} dias)
                                </p>
                                <div class="flex gap-2 overflow-x-auto pb-2 noscrollbar">
                                    ${scheduleHtml}
                                </div>
                            </div>

                            <button onclick="checkDose(${med.id})" class="w-full bg-brand-500 text-white px-4 py-3 rounded-2xl text-sm font-bold hover:bg-brand-600 transition-all shadow-brand active:scale-[0.98]">
                                Confirmar Dose de Hoje ✅
                            </button>
                        </div>
                    </div>
                `;
                list.appendChild(card);
            });
        } else {
            list.innerHTML = `<div class="text-center py-10 bg-white rounded-3xl border border-gray-100 text-gray-400 font-medium">Nenhum tratamento ativo.</div>`;
        }
    } catch(e) { console.error(e); }
}

async function checkDose(id) {
    if (!confirm('Confirmar que a dose foi administrada agora?')) return;
    try {
        const res = await fetch('api/check_medicamento.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ medicamento_id: id })
        });
        const json = await res.json();
        if (json.success) {
            loadMedicamentos();
        } else alert(json.message);
    } catch(e) {}
}

async function deleteMed(id) {
    if (!confirm('Deseja realmente excluir este tratamento? Todos os registros de doses também serão perdidos.')) return;
    try {
        const res = await fetch('api/delete_tratamento.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ id })
        });
        const json = await res.json();
        if (json.success) loadMedicamentos();
        else alert(json.message);
    } catch(e) {}
}

async function saveMed(e) {
    e.preventDefault();
    const btn = document.getElementById('saveBtn'); btn.disabled = true;
    const payload = Object.fromEntries(new FormData(e.target).entries());
    try {
        const res = await fetch('api/create_tratamento.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (json.success) {
            toggleModal('medModal');
            loadMedicamentos();
        } else alert(json.message);
    } catch (err) {} finally { btn.disabled = false; }
}

document.addEventListener('DOMContentLoaded', () => {
    loadDropdownGatos();
    loadMedicamentos();
});
</script>

<?php require_once __DIR__ . '/src/footer.php'; ?>
