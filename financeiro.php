<?php
require_once __DIR__ . '/src/auth.php';
require_login();
$page_title = "Financeiro";
require_once __DIR__ . '/src/header.php';
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Metric Cards -->
<div class="grid grid-cols-2 gap-4 mb-6 -mt-4">
    <div class="bg-green-50 rounded-2xl p-4 border border-green-100 shadow-sm text-center">
        <p class="text-xs font-bold text-green-600 uppercase mb-1">Receitas</p>
        <p class="text-xl font-extrabold text-green-700" id="tot-rec">R$ 0,00</p>
    </div>
    <div class="bg-red-50 rounded-2xl p-4 border border-red-100 shadow-sm text-center">
        <p class="text-xs font-bold text-red-600 uppercase mb-1">Despesas</p>
        <p class="text-xl font-extrabold text-red-700" id="tot-des">R$ 0,00</p>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
    <div class="bg-white p-5 rounded-3xl shadow-sm border border-gray-100">
        <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
            <span class="w-2 h-2 bg-brand-500 rounded-full"></span> Crescimento Mensal
        </h3>
        <div class="h-48">
            <canvas id="mainChart"></canvas>
        </div>
    </div>
    <div class="bg-white p-5 rounded-3xl shadow-sm border border-gray-100">
        <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
            <span class="w-2 h-2 bg-red-400 rounded-full"></span> Gastos por Categoria
        </h3>
        <div class="h-48 flex items-center justify-center">
            <canvas id="categoryChart"></canvas>
            <div id="category-empty" class="hidden text-xs text-gray-400">Sem dados este mês</div>
        </div>
    </div>
</div>

<div class="flex justify-between items-center mb-6">
    <div class="flex gap-2">
        <select id="filter-mes" onchange="loadFin()" class="bg-white border border-gray-200 rounded-xl px-2 py-1.5 text-xs font-bold outline-none shadow-sm">
            <?php
$meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
$mes_atual = date('n');
foreach ($meses as $i => $m): ?>
                <option value="<?php echo $i + 1; ?>" <?php echo($i + 1 == $mes_atual) ? 'selected' : ''; ?>><?php echo $m; ?></option>
            <?php
endforeach; ?>
        </select>
        <select id="filter-ano" onchange="loadFin()" class="bg-white border border-gray-200 rounded-xl px-2 py-1.5 text-xs font-bold outline-none shadow-sm">
            <?php
$ano_atual = date('Y');
for ($a = $ano_atual - 2; $a <= $ano_atual + 2; $a++): ?>
                <option value="<?php echo $a; ?>" <?php echo($a == $ano_atual) ? 'selected' : ''; ?>><?php echo $a; ?></option>
            <?php
endfor; ?>
        </select>
    </div>
    <div class="flex gap-2">
        <button onclick="openReport()" class="bg-brand-100 hover:bg-brand-200 text-brand-700 p-2 rounded-xl flex items-center justify-center transition-colors" title="Gerar Relatório PDF">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        </button>
        <button onclick="toggleModal('finModal', 'receita')" class="bg-green-100 hover:bg-green-200 text-green-700 p-2 rounded-xl flex items-center justify-center transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        </button>
        <button onclick="toggleModal('finModal', 'despesa')" class="bg-red-100 hover:bg-red-200 text-red-700 p-2 rounded-xl flex items-center justify-center transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
        </button>
    </div>
</div>

<!-- Tabs -->
<div class="flex border-b border-gray-200 mb-6">
    <button onclick="switchTab('transactions')" id="tab-transactions" class="px-6 py-3 font-bold text-sm border-b-2 border-brand-500 text-brand-600 transition-colors">Transações</button>
    <button onclick="switchTab('items')" id="tab-items" class="px-6 py-3 font-bold text-sm border-b-2 border-transparent text-gray-500 hover:text-brand-500 transition-colors">Tipos de Despesa</button>
</div>

<!-- Transactions Section -->
<div id="section-transactions">
    <div id="fin-list" class="space-y-3">
        <!-- Skeleton loader -->
        <div class="animate-pulse flex justify-between bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="space-y-3 w-1/2">
                <div class="h-2 bg-slate-200 rounded"></div>
                <div class="h-2 bg-slate-200 rounded w-1/2"></div>
            </div>
            <div class="h-4 bg-slate-200 rounded w-1/4"></div>
        </div>
    </div>
</div>

<!-- Items Section -->
<div id="section-items" class="hidden">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-md font-bold text-gray-700">Meus Itens de Gasto</h3>
        <button onclick="toggleItemModal()" class="bg-brand-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm">Cadastrar Item</button>
    </div>
    <div id="items-list" class="grid grid-cols-1 gap-3">
        <p class="text-center text-gray-400 text-sm py-8">Buscando itens...</p>
    </div>
</div>

<!-- Add/Edit Transaction Modal -->
<div id="finModal" class="fixed inset-0 z-[60] bg-black/50 hidden items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto transform scale-95 transition-transform duration-300" id="finModalContent">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-5" id="finModalTitle">Nova Transação</h2>
            <form id="finForm" onsubmit="saveFin(event)" class="space-y-4">
                <input type="hidden" id="fin_id" name="id">
                <input type="hidden" id="tipo" name="tipo">

                <div id="item-selection-area" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Selecionar Item</label>
                    <select id="item_id" name="item_id" onchange="onItemSelect(this)" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none bg-white">
                        <option value="">-- Gasto Avulso --</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Valor Unit. (R$)</label>
                        <input type="number" step="0.01" id="valor_unit" name="valor_unit" oninput="calculateTotal()" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade</label>
                        <input type="number" step="0.01" id="quantidade" name="quantidade" value="1" oninput="calculateTotal()" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Valor Total (R$) *</label>
                    <input type="number" step="0.01" id="valor" name="valor" required class="w-full text-2xl font-bold px-4 py-3 rounded-xl border border-gray-200 outline-none bg-gray-50">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descrição *</label>
                    <input type="text" id="descricao" name="descricao" required placeholder="Ex: Saco de Ração 10kg" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                        <select id="categoria" name="categoria" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none bg-white">
                            <option value="racao">Ração</option>
                            <option value="areia">Areia</option>
                            <option value="veterinario">Veterinário</option>
                            <option value="medicamentos">Medicamentos</option>
                            <option value="doacao">Doação</option>
                            <option value="outros">Outros</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data *</label>
                        <input type="date" id="data" name="data" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none">
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-2xl space-y-3 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-bold text-gray-700">Marcar como Pago</label>
                        <input type="checkbox" id="pago" name="pago" class="w-5 h-5 accent-brand-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Valor Pago Parcial (opcional)</label>
                        <input type="number" step="0.01" id="valor_pago" name="valor_pago" placeholder="R$ 0,00" class="w-full px-3 py-2 rounded-lg border border-gray-200 outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nota de Pagamento</label>
                        <input type="text" id="observacao_pagamento" name="observacao_pagamento" placeholder="Ex: Pago via Pix" class="w-full px-3 py-2 rounded-lg border border-gray-200 outline-none text-sm">
                    </div>
                </div>
                
                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="toggleModal('finModal')" class="flex-1 bg-gray-100 text-gray-700 font-semibold py-3 rounded-xl">Cancelar</button>
                    <button type="submit" id="saveBtn" class="flex-1 bg-brand-500 text-white font-semibold py-3 rounded-xl shadow-md">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Item Modal -->
<div id="itemModal" class="fixed inset-0 z-[60] bg-black/50 hidden items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-xl w-full max-w-sm transform scale-95 transition-transform duration-300" id="itemModalContent">
        <div class="p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4" id="itemModalTitle">Novo Tipo de Despesa</h2>
            <form id="itemForm" onsubmit="saveItem(event)" class="space-y-4">
                <input type="hidden" id="item_id_input" name="id">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Item *</label>
                    <input type="text" id="item_nome" name="nome" required placeholder="Ex: Ração Golden Gatitos" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Preço Unit. *</label>
                        <input type="number" step="0.01" id="item_preco" name="preco_unitario" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unidade</label>
                        <input type="text" id="item_unidade" name="unidade" placeholder="Ex: kg, un" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                    <select id="item_categoria" name="categoria" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none bg-white">
                        <option value="racao">Ração</option>
                        <option value="areia">Areia</option>
                        <option value="outros">Outros</option>
                    </select>
                </div>
                <div class="pt-2 flex gap-2">
                    <button type="button" onclick="toggleItemModal()" class="flex-1 bg-gray-50 text-gray-500 py-2 rounded-xl text-sm">Cancelar</button>
                    <button type="submit" class="flex-1 bg-brand-500 text-white py-2 rounded-xl text-sm font-bold shadow-sm">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let itemsData = [];

function switchTab(tab) {
    const sectionT = document.getElementById('section-transactions');
    const sectionI = document.getElementById('section-items');
    const tabT = document.getElementById('tab-transactions');
    const tabI = document.getElementById('tab-items');

    if (tab === 'transactions') {
        sectionT.classList.remove('hidden');
        sectionI.classList.add('hidden');
        tabT.className = "px-6 py-3 font-bold text-sm border-b-2 border-brand-500 text-brand-600 transition-colors";
        tabI.className = "px-6 py-3 font-bold text-sm border-b-2 border-transparent text-gray-500 hover:text-brand-500 transition-colors";
    } else {
        sectionT.classList.add('hidden');
        sectionI.classList.remove('hidden');
        tabI.className = "px-6 py-3 font-bold text-sm border-b-2 border-brand-500 text-brand-600 transition-colors";
        tabT.className = "px-6 py-3 font-bold text-sm border-b-2 border-transparent text-gray-500 hover:text-brand-500 transition-colors";
        loadItems();
    }
}

async function loadItems() {
    try {
        const res = await fetch('api/list_itens_financeiro.php');
        const json = await res.json();
        const list = document.getElementById('items-list');
        const select = document.getElementById('item_id');
        
        if (json.success) {
            itemsData = json.data;
            list.innerHTML = itemsData.length === 0 ? '<p class="text-center text-gray-400 text-sm py-8">Nenhum item cadastrado.</p>' : '';
            
            // Re-populate select (Nova Transação)
            select.innerHTML = '<option value="">-- Gasto Avulso --</option>';
            
            itemsData.forEach(item => {
                const div = document.createElement('div');
                div.className = "bg-white p-3 rounded-xl border border-gray-100 flex justify-between items-center shadow-sm hover:border-brand-200 transition-colors group";
                div.innerHTML = `
                    <div>
                        <p class="text-sm font-bold text-gray-700">${item.nome}</p>
                        <p class="text-[10px] text-gray-400 uppercase font-bold">${item.categoria} • R$ ${item.preco_unitario} / ${item.unidade}</p>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="editItem(${item.id})" class="text-gray-300 hover:text-blue-500 p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </button>
                        <button onclick="deleteItem(${item.id})" class="text-gray-300 hover:text-red-500 p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                `;
                list.appendChild(div);
                
                const opt = document.createElement('option');
                opt.value = item.id;
                opt.textContent = item.nome;
                select.appendChild(opt);
            });
        }
    } catch (e) {}
}

function toggleItemModal() {
    const el = document.getElementById('itemModal');
    if (el.classList.contains('hidden')) {
        el.classList.remove('hidden'); el.classList.add('flex');
        setTimeout(() => el.classList.remove('opacity-0'), 10);
    } else {
        el.classList.add('opacity-0');
        setTimeout(() => { el.classList.add('hidden'); el.classList.remove('flex'); }, 300);
    }
}

async function saveItem(e) {
    e.preventDefault();
    const payload = Object.fromEntries(new FormData(e.target).entries());
    const isEdit = !!payload.id;
    const url = isEdit ? 'api/update_item_financeiro.php' : 'api/create_item_financeiro.php';
    
    AppUI.loading(true, 'Salvando...');
    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN || ''},
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (json.success) {
            toggleItemModal();
            loadItems();
            AppUI.toast(isEdit ? 'Item atualizado!' : 'Item criado!', 'success');
        } else {
            AppUI.toast(json.message, 'error');
        }
    } catch (e) {
        AppUI.toast('Erro ao salvar', 'error');
    } finally {
        AppUI.loading(false);
    }
}

function editItem(id) {
    const item = itemsData.find(i => i.id == id);
    if (!item) return;
    
    toggleItemModal();
    document.getElementById('itemModalTitle').textContent = 'Editar Item';
    document.getElementById('item_id_input').value = item.id;
    document.getElementById('item_nome').value = item.nome;
    document.getElementById('item_preco').value = item.preco_unitario;
    document.getElementById('item_unidade').value = item.unidade;
    document.getElementById('item_categoria').value = item.categoria;
}

async function deleteItem(id) {
    const confirmed = await AppUI.confirm('Deseja realmente excluir este tipo de despesa?', null, 'Excluir item');
    if (!confirmed) return;
    
    try {
        const res = await fetch('api/delete_item_financeiro.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN || ''},
            body: JSON.stringify({ id })
        });
        const json = await res.json();
        if (json.success) {
            loadItems();
            AppUI.toast('Item excluído!', 'success');
        } else {
            AppUI.toast(json.message, 'error');
        }
    } catch (e) { AppUI.toast('Erro ao excluir', 'error'); }
}

function onItemSelect(select) {
    const itemId = select.value;
    const item = itemsData.find(i => i.id == itemId);
    const descInput = document.getElementById('descricao');
    const valorUnitInput = document.getElementById('valor_unit');
    const catSelect = document.getElementById('categoria');

    if (item) {
        descInput.value = item.nome;
        valorUnitInput.value = item.preco_unitario;
        catSelect.value = item.categoria;
    } else {
        descInput.value = '';
        valorUnitInput.value = '';
    }
    calculateTotal();
}

function calculateTotal() {
    const unit = parseFloat(document.getElementById('valor_unit').value) || 0;
    const qty = parseFloat(document.getElementById('quantidade').value) || 0;
    document.getElementById('valor').value = (unit * qty).toFixed(2);
}

function toggleModal(modalID, tipo = null) {
    const el = document.getElementById(modalID);
    const form = document.getElementById('finForm');
    const itemArea = document.getElementById('item-selection-area');
    
    if (el.classList.contains('hidden')) {
        form.reset();
        document.getElementById('data').value = new Date().toISOString().split('T')[0];
        
        if (tipo) {
            document.getElementById('tipo').value = tipo;
            document.getElementById('finModalTitle').textContent = tipo === 'receita' ? 'Nova Receita 🤑' : 'Nova Despesa 📉';
            
            if (tipo === 'despesa') {
                itemArea.classList.remove('hidden');
                loadItems(); // Ensure items are fresh
            } else {
                itemArea.classList.add('hidden');
            }

            if (tipo === 'receita') document.getElementById('categoria').value = 'doacao';
            else document.getElementById('categoria').value = 'racao';
        }

        el.classList.remove('hidden'); el.classList.add('flex');
        setTimeout(() => el.classList.remove('opacity-0'), 10);
    } else {
        el.classList.add('opacity-0');
        setTimeout(() => { el.classList.add('hidden'); el.classList.remove('flex'); }, 300);
    }
}

function formatDateBr(dateString) {
    const [y, m, d] = dateString.split('-');
    return `${d}/${m}`;
}

const catIcons = { 'racao': '🥣', 'areia': '📦', 'veterinario': '🩺', 'medicamentos': '💊', 'doacao': '💖', 'outros': '💵' };

// --- New Charts Implementation ---
let mainChart = null;
let categoryChart = null;

async function loadStats(m, a) {
    try {
        const res = await fetch(`api/get_finance_stats.php?mes=${m}&ano=${a}`);
        const json = await res.json();
        if (!json.success) return;

        // Render Bar Chart (Monthly)
        const ctxBar = document.getElementById('mainChart').getContext('2d');
        if (mainChart) mainChart.destroy();
        mainChart = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: json.mensal.map(m => m.label),
                datasets: [
                    { label: 'Receitas', data: json.mensal.map(m => m.receita), backgroundColor: '#14b8a6', borderRadius: 6 },
                    { label: 'Despesas', data: json.mensal.map(m => m.despesa), backgroundColor: '#ef4444', borderRadius: 6 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, grid: { display: false }, ticks: { display: false } }, x: { grid: { display: false } } }
            }
        });

        // Render Pie Chart (Categories for selected period)
        const despesasData = json.categorias_despesas;
        const ctxPie = document.getElementById('categoryChart').getContext('2d');
        if (categoryChart) categoryChart.destroy();
        
        const labels = Object.keys(despesasData);
        if (labels.length === 0) {
            document.getElementById('category-empty').classList.remove('hidden');
            document.getElementById('categoryChart').classList.add('hidden');
        } else {
            document.getElementById('category-empty').classList.add('hidden');
            document.getElementById('categoryChart').classList.remove('hidden');
            categoryChart = new Chart(ctxPie, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: Object.values(despesasData),
                        backgroundColor: ['#f43f5e', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6', '#64748b']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } } },
                    cutout: '70%'
                }
            });
        }
    } catch (e) { console.error(e); }
}

function openReport() {
    const mes = document.getElementById('filter-mes').value;
    const ano = document.getElementById('filter-ano').value;
    window.open(`relatorio_print.php?mes=${mes}&ano=${ano}`, '_blank');
}

let currentTransactions = [];

async function loadFin() {
    const mes = document.getElementById('filter-mes').value;
    const ano = document.getElementById('filter-ano').value;
    
    loadStats(mes, ano); // Fetch stats for this period
    try {
        const res = await fetch(`api/list_financeiro.php?mes=${mes}&ano=${ano}`);
        const json = await res.json();
        const list = document.getElementById('fin-list');
        list.innerHTML = '';
        
        if (json.success) {
            currentTransactions = json.data;
            document.getElementById('tot-rec').textContent = `R$ ${json.summary.total_receitas.toFixed(2).replace('.', ',')}`;
            document.getElementById('tot-des').textContent = `R$ ${json.summary.total_despesas.toFixed(2).replace('.', ',')}`;

            if (json.data.length === 0) {
                list.innerHTML = `<div class="text-center py-6 text-gray-500 text-sm">Nenhum registro no mês atual.</div>`;
                return;
            }

            json.data.forEach(r => {
                const isRec = r.tipo === 'receita';
                const el = document.createElement('div');
                const isPago = r.pago == 1;
                const valorFalta = parseFloat(r.valor) - parseFloat(r.valor_pago);
                
                el.className = "bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center hover:bg-gray-50 transition-colors group relative overflow-hidden";
                el.innerHTML = `
                    ${!isPago && !isRec ? '<div class="absolute left-0 top-0 bottom-0 w-1 bg-red-400"></div>' : ''}
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-xl bg-gray-50 border border-gray-100">${catIcons[r.categoria] || '💵'}</div>
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="font-bold text-gray-800 text-sm">${r.descricao}</p>
                                ${isPago ? '<span class="text-[8px] bg-green-100 text-green-600 px-1.5 py-0.5 rounded-full font-black uppercase">Faturado</span>' : (isRec ? '' : '<span class="text-[8px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded-full font-black uppercase">Pendente</span>')}
                            </div>
                            <p class="text-xs text-gray-400 capitalize">${r.categoria} • ${formatDateBr(r.data)} ${r.quantidade > 1 ? `• Qtde: ${r.quantidade}` : ''}</p>
                            ${!isPago && r.valor_pago > 0 ? `<p class="text-[9px] text-orange-600 font-bold">Pago: R$ ${parseFloat(r.valor_pago).toFixed(2)} | Falta: R$ ${valorFalta.toFixed(2)}</p>` : ''}
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <p class="font-bold ${isRec ? 'text-green-600' : 'text-red-600'} text-right">
                            ${isRec ? '+' : '-'} R$ ${parseFloat(r.valor).toFixed(2).replace('.', ',')}
                        </p>
                        <div class="flex gap-1">
                            <button onclick="duplicateFin(${r.id})" class="text-gray-300 hover:text-brand-500 p-1 transition-colors" title="Duplicar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                            </button>
                            <button onclick="editFin(${r.id})" class="text-gray-300 hover:text-blue-500 p-1 transition-colors" title="Editar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            <button onclick="deleteFin(${r.id})" class="text-gray-300 hover:text-red-500 p-1 transition-colors" title="Excluir">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                `;
                list.appendChild(el);
            });
        }
    } catch(e) {}
}

async function deleteFin(id) {
    const confirmed = await AppUI.confirm('Deseja realmente excluir este registro?', null, 'Excluir transação');
    if (!confirmed) return;
    
    AppUI.loading(true, 'Excluindo...');
    try {
        const res = await fetch('api/delete_financeiro.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN || ''},
            body: JSON.stringify({ id })
        });
        const json = await res.json();
        if (json.success) {
            loadFin();
            AppUI.toast(json.message || 'Excluído com sucesso!', 'success');
        } else {
            AppUI.toast(json.message || 'Erro ao excluir', 'error');
        }
    } catch(e) {
        AppUI.toast('Erro de conexão', 'error');
    } finally {
        AppUI.loading(false);
    }
}

function editFin(id) {
    const fin = currentTransactions.find(t => t.id == id);
    if (!fin) return;
    
    toggleModal('finModal', fin.tipo);
    document.getElementById('finModalTitle').textContent = `Editar ${fin.tipo === 'receita' ? 'Receita' : 'Despesa'}`;
    document.getElementById('fin_id').value = fin.id;
    document.getElementById('item_id').value = fin.item_id || '';
    document.getElementById('descricao').value = fin.descricao;
    document.getElementById('categoria').value = fin.categoria;
    document.getElementById('valor').value = fin.valor;
    document.getElementById('data').value = fin.data;
    
    // New fields
    document.getElementById('pago').checked = fin.pago == 1;
    document.getElementById('valor_pago').value = fin.valor_pago || '';
    document.getElementById('observacao_pagamento').value = fin.observacao_pagamento || '';
}

async function duplicateFin(id) {
    const confirmed = await AppUI.confirm('Deseja criar uma cópia deste registro para hoje?', null, 'Duplicar transação');
    if (!confirmed) return;
    
    try {
        const res = await fetch('api/duplicate_financeiro.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN || ''},
            body: JSON.stringify({ id })
        });
        const json = await res.json();
        if (json.success) {
            loadFin();
            AppUI.toast('Registro duplicado!', 'success');
        } else {
            AppUI.toast(json.message, 'error');
        }
    } catch(e) { AppUI.toast('Erro ao duplicar', 'error'); }
}

async function saveFin(e) {
    e.preventDefault();
    const btn = document.getElementById('saveBtn'); 
    btn.disabled = true;
    AppUI.loading(true, 'Salvando...');
    
    const payload = Object.fromEntries(new FormData(e.target).entries());
    
    try {
        const res = await fetch('api/create_receita.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN || ''},
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        
        if (json.success) {
            toggleModal('finModal');
            loadFin();
            AppUI.toast(json.message || 'Salvo com sucesso!', 'success');
        } else {
            AppUI.toast(json.message || 'Erro ao salvar', 'error');
        }
    } catch (err) {
        AppUI.toast('Erro de conexão', 'error');
    } finally { 
        btn.disabled = false;
        AppUI.loading(false);
    }
}

// Support for quick actions
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('action') === 'despesa') {
    setTimeout(() => toggleModal('finModal', 'despesa'), 100);
}

document.addEventListener('DOMContentLoaded', loadFin);
</script>

<?php require_once __DIR__ . '/src/footer.php'; ?>
