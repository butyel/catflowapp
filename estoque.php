<?php
require_once __DIR__ . '/src/auth.php';
require_login();
$page_title = "Gestão de Estoque";
require_once __DIR__ . '/src/header.php';
?>

<div class="mb-8 -mt-4">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-black text-gray-800 tracking-tight">Estoque</h1>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Controle de insumos e suprimentos</p>
        </div>
        <button onclick="openItemModal()" class="bg-brand-500 hover:bg-brand-600 text-white rounded-2xl px-5 py-3 font-bold shadow-brand transition-all active:scale-95 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            Novo Item
        </button>
    </div>

    <!-- Tabs Navigation -->
    <div class="flex gap-2 p-1 bg-gray-100 rounded-2xl mb-6 w-fit">
        <button onclick="switchTab('dashboard')" id="tab-dashboard" class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all bg-white text-gray-800 shadow-sm border border-gray-100">
            Painel Geral
        </button>
        <button onclick="switchTab('master')" id="tab-master" class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all text-gray-500 hover:text-gray-700">
            Itens Padrões
        </button>
    </div>
</div>

<!-- Tab: Dashboard (Stock Balance) -->
<div id="view-dashboard" class="space-y-6">
    <div id="stock-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Stock balance items populated via JS -->
    </div>
</div>

<!-- Tab: Master Data (Items Management) -->
<div id="view-master" class="hidden animate-in fade-in duration-300">
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Item</th>
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Categoria</th>
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Mínimo</th>
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Ações</th>
                </tr>
            </thead>
            <tbody id="master-list">
                <!-- Items list populated via JS -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Move Stock (Entry/Exit) -->
<div id="moveModal" class="fixed inset-0 z-[65] bg-black/60 hidden items-center justify-center p-4 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-sm p-8 transform scale-95 transition-transform duration-300" id="moveModalContent">
        <div class="mb-6">
            <h2 class="text-2xl font-black text-gray-800" id="moveTitle">Movimentação</h2>
            <p id="moveItemName" class="text-sm font-bold text-brand-600 uppercase tracking-widest mt-1"></p>
        </div>
        <form id="moveForm" onsubmit="saveMovement(event)" class="space-y-5">
            <input type="hidden" name="estoque_id" id="move_item_id">
            <input type="hidden" name="tipo" id="move_tipo">
            
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Quantidade (<span id="moveUnit">un</span>)</label>
                <input type="number" step="0.01" name="quantidade" id="move_quant" required placeholder="0.00" class="w-full px-5 py-3.5 rounded-2xl border-2 border-gray-100 focus:border-brand-500 outline-none text-xl font-bold transition-all">
            </div>

            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Observação (opcional)</label>
                <input type="text" name="observacao" placeholder="Ex: Compra do mês, Uso diário" class="w-full px-5 py-3.5 rounded-2xl border-2 border-gray-100 focus:border-brand-500 outline-none text-sm transition-all">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="toggleModal('moveModal')" class="flex-1 bg-gray-50 text-gray-500 py-4 rounded-2xl font-bold hover:bg-gray-100 transition-colors">Cancelar</button>
                <button type="submit" id="moveSubmitBtn" class="flex-1 text-white py-4 rounded-2xl font-bold shadow-lg transition-all active:scale-95"></button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Master Item (Create/Edit) -->
<div id="itemModal" class="fixed inset-0 z-[65] bg-black/60 hidden items-center justify-center p-4 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md p-8 transform scale-95 transition-transform duration-300" id="itemModalContent">
        <h2 class="text-2xl font-black text-gray-800 mb-6" id="itemModalTitle">Configurar Item</h2>
        <form id="itemForm" onsubmit="saveItem(event)" class="space-y-4">
            <input type="hidden" name="id" id="item_id">
            
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nome do Item *</label>
                <input type="text" name="nome_item" id="nome_item" required placeholder="Ex: Ração Golden Gatitos" class="w-full px-5 py-3.5 rounded-2xl border-2 border-gray-100 focus:border-brand-500 outline-none text-sm transition-all font-bold">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Categoria</label>
                    <select name="categoria" id="categoria" required class="w-full px-5 py-3.5 rounded-2xl border-2 border-gray-100 focus:border-brand-500 outline-none bg-white font-bold text-sm">
                        <option value="racao">Ração</option>
                        <option value="areia">Areia</option>
                        <option value="medicamento">Medicamento</option>
                        <option value="limpeza">Limpeza</option>
                        <option value="outro">Outro</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Unidade</label>
                    <input type="text" name="unidade" id="unidade" placeholder="Ex: kg, un" class="w-full px-5 py-3.5 rounded-2xl border-2 border-gray-100 focus:border-brand-500 outline-none text-sm font-bold">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Estoque Mínimo</label>
                    <input type="number" step="0.01" name="estoque_minimo" id="estoque_minimo" value="0" class="w-full px-5 py-3.5 rounded-2xl border-2 border-gray-100 focus:border-brand-500 outline-none text-sm font-bold">
                </div>
                <div id="initial-stock-area">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Estoque Inicial</label>
                    <input type="number" step="0.01" name="quantidade_atual" id="quantidade_atual" value="0" class="w-full px-5 py-3.5 rounded-2xl border-2 border-gray-100 focus:border-brand-500 outline-none text-sm font-bold">
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="toggleModal('itemModal')" class="flex-1 bg-gray-50 text-gray-500 py-4 rounded-2xl font-bold">Cancelar</button>
                <button type="submit" class="flex-1 bg-brand-500 text-white py-4 rounded-2xl font-bold shadow-brand">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
let currentInventory = [];
const icons = { 'racao': '🥣', 'areia': '📦', 'medicamento': '💊', 'limpeza': '🧹', 'outro': '📦' };

async function loadStock() {
    try {
        const res = await fetch('api/inventory.php?action=list');
        const json = await res.json();
        if (!json.success) return;

        currentInventory = json.data;
        renderDashboard();
        renderMaster();
    } catch(e) { console.error(e); }
}

function renderDashboard() {
    const list = document.getElementById('stock-grid');
    list.innerHTML = '';
    
    if (currentInventory.length === 0) {
        list.innerHTML = `<div class="col-span-full text-center py-20 bg-white rounded-[2rem] border border-gray-100 shadow-sm"><p class="text-gray-400 font-bold">Nenhum item em estoque.</p></div>`;
        return;
    }

    currentInventory.forEach(item => {
        const isLow = parseFloat(item.quantidade_atual) <= parseFloat(item.estoque_minimo);
        const card = document.createElement('div');
        card.className = "bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group";
        card.innerHTML = `
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center text-3xl group-hover:bg-brand-50 group-hover:border-brand-100 transition-colors">${icons[item.categoria] || '📦'}</div>
                ${isLow ? '<span class="px-2 py-1 bg-red-100 text-red-600 text-[8px] font-black rounded-lg uppercase tracking-wider animate-pulse">Estoque Baixo</span>' : ''}
            </div>
            
            <h3 class="font-black text-gray-800 mb-1 truncate text-lg">${item.nome_item}</h3>
            <div class="flex items-baseline gap-1 mb-6">
                <p class="text-3xl font-black ${isLow ? 'text-red-500' : 'text-brand-600'}">${parseFloat(item.quantidade_atual).toString()}</p>
                <span class="text-xs font-black text-gray-400 uppercase tracking-widest">${item.unidade}</span>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <button onclick="openMoveModal(${item.id}, 'saida')" class="bg-red-50 hover:bg-red-100 text-red-600 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-colors flex items-center justify-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"></path></svg>
                    Baixa
                </button>
                <button onclick="openMoveModal(${item.id}, 'entrada')" class="bg-green-50 hover:bg-green-100 text-green-600 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-colors flex items-center justify-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                    Entrada
                </button>
            </div>
        `;
        list.appendChild(card);
    });
}

function renderMaster() {
    const list = document.getElementById('master-list');
    list.innerHTML = '';
    
    currentInventory.forEach(item => {
        const row = document.createElement('tr');
        row.className = "border-b border-gray-50 hover:bg-gray-50/50 transition-colors";
        row.innerHTML = `
            <td class="px-6 py-4">
                <p class="font-bold text-gray-700 text-sm">${item.nome_item}</p>
                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">${item.unidade}</p>
            </td>
            <td class="px-6 py-4">
                <span class="px-2 py-1 bg-gray-100 rounded-lg text-[9px] font-black uppercase tracking-widest text-gray-500">${item.categoria}</span>
            </td>
            <td class="px-6 py-4">
                <p class="font-bold text-gray-600 text-sm">${item.estoque_minimo} <span class="text-[10px] text-gray-400 uppercase">${item.unidade}</span></p>
            </td>
            <td class="px-6 py-4 text-right">
                <div class="flex justify-end gap-2">
                    <button onclick="editItem(${item.id})" class="text-gray-300 hover:text-blue-500 p-1.5 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    </button>
                    <button onclick="deleteItem(${item.id})" class="text-gray-300 hover:text-red-500 p-1.5 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            </td>
        `;
        list.appendChild(row);
    });
}

function switchTab(tab) {
    const btnDash = document.getElementById('tab-dashboard');
    const btnMaster = document.getElementById('tab-master');
    const viewDash = document.getElementById('view-dashboard');
    const viewMaster = document.getElementById('view-master');

    if (tab === 'dashboard') {
        btnDash.className = "px-6 py-2.5 rounded-xl text-sm font-bold transition-all bg-white text-gray-800 shadow-sm border border-gray-100";
        btnMaster.className = "px-6 py-2.5 rounded-xl text-sm font-bold transition-all text-gray-500 hover:text-gray-700";
        viewDash.classList.remove('hidden');
        viewMaster.classList.add('hidden');
    } else {
        btnMaster.className = "px-6 py-2.5 rounded-xl text-sm font-bold transition-all bg-white text-gray-800 shadow-sm border border-gray-100";
        btnDash.className = "px-6 py-2.5 rounded-xl text-sm font-bold transition-all text-gray-500 hover:text-gray-700";
        viewDash.classList.add('hidden');
        viewMaster.classList.remove('hidden');
    }
}

function openMoveModal(id, tipo) {
    const item = currentInventory.find(i => i.id === id);
    if (!item) return;

    document.getElementById('move_item_id').value = id;
    document.getElementById('move_tipo').value = tipo;
    document.getElementById('moveItemName').textContent = item.nome_item;
    document.getElementById('moveUnit').textContent = item.unidade;
    document.getElementById('move_quant').value = '';
    
    const btn = document.getElementById('moveSubmitBtn');
    if (tipo === 'entrada') {
        document.getElementById('moveTitle').textContent = 'Registrar Entrada';
        btn.textContent = 'Confirmar Entrada';
        btn.className = "flex-1 bg-green-500 text-white py-4 rounded-2xl font-bold shadow-lg shadow-green-200 active:scale-95";
    } else {
        document.getElementById('moveTitle').textContent = 'Registrar Saída (Baixa)';
        btn.textContent = 'Confirmar Baixa';
        btn.className = "flex-1 bg-red-500 text-white py-4 rounded-2xl font-bold shadow-lg shadow-red-200 active:scale-95";
    }
    
    toggleModal('moveModal');
}

async function saveMovement(e) {
    e.preventDefault();
    const payload = Object.fromEntries(new FormData(e.target));
    try {
        const res = await fetch('api/inventory.php?action=move', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (json.success) {
            toggleModal('moveModal');
            loadStock();
        } else alert(json.message);
    } catch(e) {
        console.error(e);
        alert('Erro ao processar movimentação. Verifique o console.');
    }
}

function openItemModal() {
    document.getElementById('itemForm').reset();
    document.getElementById('item_id').value = '';
    document.getElementById('itemModalTitle').textContent = 'Configurar Item';
    document.getElementById('initial-stock-area').classList.remove('hidden');
    toggleModal('itemModal');
}

function editItem(id) {
    const item = currentInventory.find(i => i.id === id);
    if (!item) return;
    
    document.getElementById('item_id').value = item.id;
    document.getElementById('nome_item').value = item.nome_item;
    document.getElementById('categoria').value = item.categoria;
    document.getElementById('unidade').value = item.unidade;
    document.getElementById('estoque_minimo').value = item.estoque_minimo;
    document.getElementById('initial-stock-area').classList.add('hidden'); // Hide during edit
    document.getElementById('itemModalTitle').textContent = 'Editar Item';
    
    toggleModal('itemModal');
}

async function saveItem(e) {
    e.preventDefault();
    const payload = Object.fromEntries(new FormData(e.target));
    try {
        const res = await fetch('api/inventory.php?action=upsert', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        });
        
        const contentType = res.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            const text = await res.text();
            console.error('Resposta não é JSON:', text);
            throw new Error('Servidor retornou um formato inválido. Verifique o console.');
        }

        const json = await res.json();
        if (json.success) {
            toggleModal('itemModal');
            loadStock();
        } else alert(json.message);
    } catch(e) {
        console.error(e);
        alert('Erro ao salvar item: ' + e.message);
    }
}

async function deleteItem(id) {
    if (!confirm('Deseja realmente remover este item? O saldo atual será perdido.')) return;
    try {
        const res = await fetch('api/inventory.php?action=delete', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ id })
        });
        const json = await res.json();
        if (json.success) loadStock();
        else alert(json.message);
    } catch(e) {
        console.error(e);
        alert('Erro ao remover item.');
    }
}

function toggleModal(id) {
    const el = document.getElementById(id);
    const content = document.getElementById(id + 'Content');
    if (el.classList.contains('hidden')) {
        el.classList.replace('hidden', 'flex');
        setTimeout(() => { el.classList.remove('opacity-0'); content.classList.replace('scale-95', 'scale-100'); }, 10);
    } else {
        el.classList.add('opacity-0'); 
        content.classList.replace('scale-100', 'scale-95');
        setTimeout(() => el.classList.replace('flex', 'hidden'), 300);
    }
}

document.addEventListener('DOMContentLoaded', loadStock);
</script>

<?php require_once __DIR__ . '/src/footer.php'; ?>
