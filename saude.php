<?php
require_once __DIR__ . '/src/auth.php';
require_login();

// If coming from gatos.php, we preselect the cat
$gato_id = isset($_GET['gato_id']) ? (int)$_GET['gato_id'] : 0;
$gato_nome = isset($_GET['nome']) ? htmlspecialchars($_GET['nome']) : 'Geral';
$page_title = $gato_id ? "Saúde: $gato_nome" : "Visão de Saúde Geral";

require_once __DIR__ . '/src/header.php';
?>

<!-- Health Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6 -mt-4">
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 text-center">
        <p class="text-2xl font-extrabold text-green-500" id="stat-vacinas">-</p>
        <p class="text-[10px] font-bold text-gray-400 uppercase">Vacinas</p>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 text-center">
        <p class="text-2xl font-extrabold text-blue-500" id="stat-consultas">-</p>
        <p class="text-[10px] font-bold text-gray-400 uppercase">Consultas</p>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 text-center">
        <p class="text-2xl font-extrabold text-red-500" id="stat-cirurgias">-</p>
        <p class="text-[10px] font-bold text-gray-400 uppercase">Cirurgias</p>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 text-center">
        <p class="text-2xl font-extrabold text-orange-500" id="stat-retornos">-</p>
        <p class="text-[10px] font-bold text-gray-400 uppercase">Retornos</p>
    </div>
</div>

<!-- Tabs -->
<div class="flex bg-white rounded-xl shadow-sm mb-6 -mt-4 p-1 overflow-x-auto">
    <button class="flex-1 py-2 px-4 text-sm font-bold bg-brand-50 text-brand-600 rounded-lg whitespace-nowrap">Histórico & Vacinas</button>
    <button onclick="window.location.href='medicamentos.php<?php echo $gato_id ? '?gato_id=' . $gato_id : ''; ?>'" class="flex-1 py-2 px-4 text-sm font-medium text-gray-500 hover:bg-gray-50 rounded-lg whitespace-nowrap transition-colors">Medicamentos</button>
</div>

<!-- Header Actions -->
<div class="flex justify-between items-center mb-6">
    <div class="relative w-2/3">
        <!-- We will populate this visually or via JS with cats -->
        <select id="gato_selector" onchange="changeGato(this.value)" class="w-full pl-4 pr-10 py-2.5 rounded-xl border border-gray-200 outline-none bg-white shadow-sm appearance-none">
            <option value="0">Todos os Gatos</option>
            <!-- Populated via JS -->
        </select>
        <svg class="w-5 h-5 text-gray-400 absolute right-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
    </div>
    <button onclick="toggleModal('saudeModal')" class="bg-brand-500 hover:bg-brand-600 text-white rounded-xl p-2 w-10 h-10 flex items-center justify-center shadow-md">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
    </button>
</div>

<!-- Timeline List -->
<div class="relative border-l-2 border-brand-100 ml-3 pl-5 space-y-6" id="saude-list">
    <!-- Skeleton loader -->
    <div class="animate-pulse flex space-x-4 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <div class="flex-1 space-y-3 py-1">
            <div class="h-2 bg-slate-200 rounded w-1/4"></div>
            <div class="h-2 bg-slate-200 rounded w-3/4"></div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div id="saudeModal" class="fixed inset-0 z-[60] bg-black/50 hidden items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto transform scale-95 transition-transform duration-300" id="saudeModalContent">
        <div class="p-6">
            <div class="flex justify-between items-center mb-5">
                <h2 class="text-xl font-bold text-gray-800">Novo Evento Médico</h2>
                <button onclick="toggleModal('saudeModal')" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form id="saudeForm" onsubmit="saveSaude(event)" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gato *</label>
                    <select id="modal_gato_id" name="gato_id" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-brand-500 outline-none bg-white">
                        <!-- Populated via JS -->
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
                        <select id="tipo" name="tipo" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-brand-500 outline-none bg-white">
                            <option value="vacina">Vacina</option>
                            <option value="consulta">Consulta</option>
                            <option value="cirurgia">Cirurgia</option>
                            <option value="exame">Exame</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data Evento *</label>
                        <input type="date" id="data_evento" name="data_evento" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descrição / Nome (Vacina) *</label>
                    <input type="text" id="descricao" name="descricao" required placeholder="Ex: Vacina V5" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Próxima Data (Retorno/Reforço)</label>
                    <input type="date" id="proxima_data" name="proxima_data" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none text-gray-500">
                </div>
                
                <div class="pt-2 flex gap-3">
                    <button type="button" onclick="toggleModal('saudeModal')" class="flex-1 bg-gray-100 text-gray-700 hover:bg-gray-200 font-semibold py-3 rounded-xl transition-colors">Cancelar</button>
                    <button type="submit" id="saveBtn" class="flex-1 bg-brand-500 text-white hover:bg-brand-600 font-semibold py-3 rounded-xl transition-colors shadow-md shadow-brand-500/30">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const currentGatoId = <?php echo $gato_id; ?>;

function toggleModal(modalID) {
    const el = document.getElementById(modalID);
    const content = document.getElementById('saudeModalContent');
    const form = document.getElementById('saudeForm');
    
    if (el.classList.contains('hidden')) {
        form.reset();
        document.getElementById('data_evento').value = new Date().toISOString().split('T')[0];
        if (currentGatoId) {
            document.getElementById('modal_gato_id').value = currentGatoId;
        }

        el.classList.remove('hidden');
        el.classList.add('flex');
        setTimeout(() => {
            el.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 10);
    } else {
        el.classList.add('opacity-0');
        content.classList.add('scale-95');
        setTimeout(() => {
            el.classList.add('hidden');
            el.classList.remove('flex');
        }, 300);
    }
}

async function loadDropdownGatos() {
    try {
        const res = await fetch('api/list_gatos.php');
        const json = await res.json();
        if (json.success) {
            const selectMain = document.getElementById('gato_selector');
            const selectModal = document.getElementById('modal_gato_id');
            
            json.data.forEach(g => {
                const optMain = new Option(g.nome, g.id);
                const optModal = new Option(g.nome, g.id);
                selectMain.add(optMain);
                selectModal.add(optModal);
            });

            if (currentGatoId) selectMain.value = currentGatoId;
        }
    } catch(e) {}
}

function changeGato(id) {
    if (id == 0) window.location.href = 'saude.php';
    else window.location.href = `saude.php?gato_id=${id}&nome=Gato`;
}

function formatDateBr(dateString) {
    if (!dateString) return '';
    const [y, m, d] = dateString.split('-');
    return `${d}/${m}/${y}`;
}

function getIconForTipo(tipo) {
    const icons = {
        'vacina': '💉',
        'consulta': '🩺',
        'cirurgia': '✂️',
        'exame': '🔬'
    };
    return icons[tipo] || '📋';
}

function getColorForTipo(tipo) {
    const colors = {
        'vacina': 'text-green-500 bg-green-100',
        'consulta': 'text-blue-500 bg-blue-100',
        'cirurgia': 'text-red-500 bg-red-100',
        'exame': 'text-purple-500 bg-purple-100'
    };
    return colors[tipo] || 'text-gray-500 bg-gray-100';
}

async function loadSaude() {
    try {
        const url = currentGatoId ? `api/list_saude.php?gato_id=${currentGatoId}` : 'api/list_saude.php';
        const res = await fetch(url);
        const json = await res.json();
        
        const list = document.getElementById('saude-list');
        list.innerHTML = '';
        
        if (!json.success || json.data.length === 0) {
            list.className = "mt-4 text-center py-10 bg-white rounded-2xl border border-gray-100 shadow-sm";
            list.innerHTML = `<p class="text-gray-500 font-medium">Nenhum evento registrado.</p>`;
            document.getElementById('stat-vacinas').textContent = '0';
            document.getElementById('stat-consultas').textContent = '0';
            document.getElementById('stat-cirurgias').textContent = '0';
            document.getElementById('stat-retornos').textContent = '0';
            return;
        }

        let stats = { vacina: 0, consulta: 0, cirurgia: 0, exames: 0, retornos: 0 };
        json.data.forEach(s => {
            if (s.tipo === 'vacina') stats.vacina++;
            else if (s.tipo === 'consulta') stats.consulta++;
            else if (s.tipo === 'cirurgia') stats.cirurgia++;
            else if (s.tipo === 'exame') stats.exames++;
            if (s.proxima_data) stats.retornos++;
        });
        
        document.getElementById('stat-vacinas').textContent = stats.vacina;
        document.getElementById('stat-consultas').textContent = stats.consulta;
        document.getElementById('stat-cirurgias').textContent = stats.cirurgia;
        document.getElementById('stat-retornos').textContent = stats.retornos;

        json.data.forEach(s => {
            const div = document.createElement('div');
            div.className = "relative bg-white p-4 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow";
            
            const colorClass = getColorForTipo(s.tipo);
            const icon = getIconForTipo(s.tipo);

            div.innerHTML = `
                <div class="absolute -left-9 top-1/2 transform -translate-y-1/2 w-6 h-6 rounded-full bg-brand-500 border-4 border-[#f8fafc]"></div>
                
                <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full flex justify-center items-center text-sm ${colorClass}">${icon}</span>
                        <div>
                            <h3 class="font-bold text-gray-800">${s.descricao}</h3>
                            <p class="text-xs text-gray-500 capitalize">${s.tipo} ${!currentGatoId && s.gato_nome ? `<span class="font-semibold text-brand-600 ml-1">• ${s.gato_nome}</span>` : ''}</p>
                        </div>
                    </div>
                    <button onclick="deleteSaude(${s.id})" class="text-gray-300 hover:text-red-500 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
                
                <div class="grid grid-cols-2 gap-2 mt-3 p-2 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase">Data Evento</p>
                        <p class="text-sm font-medium text-gray-700">${formatDateBr(s.data_evento)}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase">Próximo/Retorno</p>
                        <p class="text-sm font-medium ${s.proxima_data ? 'text-orange-600 font-bold' : 'text-gray-500'}">${s.proxima_data ? formatDateBr(s.proxima_data) : '-'}</p>
                    </div>
                </div>
            `;
            list.appendChild(div);
        });
    } catch(e) { console.error(e); }
}

async function deleteSaude(id) {
    const confirmed = await AppUI.confirm('Deseja realmente excluir este evento médico?', null, 'Excluir Evento');
    if (!confirmed) return;
    
    AppUI.loading(true, 'Excluindo...');
    try {
        const res = await fetch('api/delete_saude.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN || ''},
            body: JSON.stringify({ id })
        });
        const json = await res.json();
        if (json.success) {
            loadSaude();
            AppUI.toast('Evento excluído!', 'success');
        } else {
            AppUI.toast(json.message, 'error');
        }
    } catch(e) {
        AppUI.toast('Erro ao excluir', 'error');
    } finally {
        AppUI.loading(false);
    }
}

async function saveSaude(e) {
    e.preventDefault();
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.innerText = 'Salvando...';
    AppUI.loading(true, 'Salvando...');
    
    const form = e.target;
    const payload = Object.fromEntries(new FormData(form).entries());
    
    try {
        const res = await fetch('api/create_vacina.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN || ''},
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        
        if (json.success) {
            toggleModal('saudeModal');
            loadSaude();
            AppUI.toast('Evento registrado!', 'success');
        } else {
            AppUI.toast(json.message || 'Erro ao salvar', 'error');
        }
    } catch (err) {
        AppUI.toast('Erro ao salvar', 'error');
    } finally {
        btn.disabled = false;
        btn.innerText = 'Salvar';
        AppUI.loading(false);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    loadDropdownGatos();
    loadSaude();
});
</script>

<?php require_once __DIR__ . '/src/footer.php'; ?>
