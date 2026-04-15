<?php
require_once __DIR__ . '/src/auth.php';
require_login();
$page_title = "Meus Gatos";
require_once __DIR__ . '/src/header.php';
?>

<!-- Header Actions -->
<div class="flex justify-between items-center mb-8 -mt-6">
    <div class="relative w-full mr-3">
        <input type="text" id="search" placeholder="Buscar parceiro..." class="w-full pl-12 pr-4 py-3.5 rounded-[1.5rem] border border-white shadow-premium focus:ring-4 focus:ring-brand-500/10 outline-none transition-all placeholder:text-gray-300">
        <svg class="w-6 h-6 text-brand-500 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
    </div>
    <button onclick="toggleModal('gatoModal')" class="bg-brand-500 hover:bg-brand-600 text-white rounded-2xl p-2 w-14 h-14 flex items-center justify-center shadow-brand transition-all active:scale-90 flex-shrink-0">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
    </button>
</div>

<!-- List Content -->
<div id="gatos-list" class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="animate-pulse flex space-x-4 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <div class="rounded-full bg-slate-200 h-16 w-16"></div>
        <div class="flex-1 space-y-3 py-1">
            <div class="h-2 bg-slate-200 rounded"></div>
            <div class="space-y-3">
                <div class="grid grid-cols-3 gap-4">
                    <div class="h-2 bg-slate-200 rounded col-span-2"></div>
                    <div class="h-2 bg-slate-200 rounded col-span-1"></div>
                </div>
                <div class="h-2 bg-slate-200 rounded"></div>
            </div>
        </div>
    </div>
</div>
<div id="pagination-container" class="mb-6"></div>

<!-- Add/Edit Modal -->
<div id="gatoModal" class="fixed inset-0 z-[60] bg-black/60 hidden items-center justify-center p-4 opacity-0 transition-opacity duration-300 backdrop-blur-sm">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md max-h-[92vh] overflow-y-auto transform scale-95 transition-transform duration-300 border border-gray-100" id="gatoModalContent">
        <div class="p-8">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-display font-extrabold text-gray-800" id="modalTitle">Cadastrar Gato</h2>
                <button onclick="toggleModal('gatoModal')" class="bg-gray-50 p-2 rounded-full text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form id="gatoForm" onsubmit="saveGato(event)" class="space-y-4">
                <input type="hidden" id="gato_id" name="id">
                <input type="hidden" name="csrf_token" id="csrf_token">
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                        <input type="text" id="nome" name="nome" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-brand-500 focus:ring focus:ring-brand-200 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ala / Setor</label>
                        <select id="ala_id" name="ala_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-brand-500 outline-none bg-white">
                            <option value="">Selecione uma Ala...</option>
                            <!-- Populated via JS -->
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Raça</label>
                        <input type="text" id="raca" name="raca" placeholder="Ex: Siamês" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cor / Padrão</label>
                        <input type="text" id="cor_padrao" name="cor_padrao" placeholder="Ex: Tabby" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none">
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sexo *</label>
                        <select id="sexo" name="sexo" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-brand-500 outline-none bg-white">
                            <option value="M">Macho</option>
                            <option value="F">Fêmea</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="status" name="status" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-brand-500 outline-none bg-white">
                            <option value="ativo">Ativo</option>
                            <option value="tratamento">Em Tratamento</option>
                            <option value="Doado">Doado</option>
                            <option value="aposentado">Aposentado</option>
                            <option value="óbito">Óbito</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data de Nasc.</label>
                        <input type="date" id="data_nascimento" name="data_nascimento" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No Microchip</label>
                        <input type="text" id="microchip" name="microchip" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pedigree / Registro</label>
                    <input type="text" id="pedigree" name="pedigree" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 text-red-600 font-bold">Doenças Pré-existentes / Alergias</label>
                    <textarea id="doencas_pre_existentes" name="doencas_pre_existentes" rows="2" class="w-full px-4 py-2.5 rounded-xl border-2 border-red-100 focus:border-red-400 outline-none resize-none" placeholder="Ex: Atopia, Alergia a picada de pulga..."></textarea>
                </div>

                <div class="flex items-center p-3 bg-gray-50 rounded-xl border border-gray-100">
                    <input id="castrado" name="castrado" type="checkbox" value="1" class="w-5 h-5 text-brand-600 bg-gray-100 border-gray-300 rounded focus:ring-brand-500">
                    <label for="castrado" class="ml-2 text-sm font-medium text-gray-900">Gato Castrado</label>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Idade Aprox.</label>
                        <input type="text" id="idade" name="idade" placeholder="Ex: 2 anos" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Peso (kg)</label>
                        <input type="number" step="0.01" id="peso" name="peso" placeholder="Ex: 4.5" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Histórico Completo</label>
                    <textarea id="historico" name="historico" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none resize-none" placeholder="Histórico médico, resgate, etc."></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto do Gato</label>
                    <div class="flex items-center gap-4">
                        <div id="cat-photo-preview" class="w-20 h-20 bg-gray-100 rounded-xl flex items-center justify-center overflow-hidden border-2 border-dashed border-gray-300">
                            <span class="text-xs text-gray-400">Sem foto</span>
                        </div>
                        <div class="flex-1">
                            <input type="file" id="cat-photo-input" name="foto_file" accept="image/*" onchange="previewCatPhoto(this)" class="hidden">
                            <button type="button" onclick="document.getElementById('cat-photo-input').click()" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50">
                                Selecionar / Tirar Foto
                            </button>
                        </div>
                    </div>
                </div>
                    <button type="button" onclick="toggleModal('gatoModal')" class="flex-1 bg-gray-50 text-gray-500 hover:bg-gray-100 font-bold py-4 rounded-2xl transition-all">Cancelar</button>
                    <button type="submit" id="saveBtn" class="flex-1 bg-brand-500 text-white hover:bg-brand-600 font-bold py-4 rounded-2xl transition-all shadow-brand active:scale-95">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let gatosData = [];
let paginationData = null;

function toggleModal(modalID, gato = null) {
    const el = document.getElementById(modalID);
    const content = document.getElementById('gatoModalContent');
    const form = document.getElementById('gatoForm');
    
    if (el.classList.contains('hidden')) {
        // Open
        if (gato) {
            document.getElementById('modalTitle').textContent = 'Editar Gato';
            document.getElementById('gato_id').value = gato.id;
            document.getElementById('nome').value = gato.nome;
            document.getElementById('sexo').value = gato.sexo;
            document.getElementById('status').value = gato.status;
            document.getElementById('castrado').checked = gato.castrado == 1;
            document.getElementById('idade').value = gato.idade || '';
            document.getElementById('peso').value = gato.peso || '';
            document.getElementById('raca').value = gato.raca || '';
            document.getElementById('data_nascimento').value = gato.data_nascimento || '';
            document.getElementById('cor_padrao').value = gato.cor_padrao || '';
            document.getElementById('microchip').value = gato.microchip || '';
            document.getElementById('pedigree').value = gato.pedigree || '';
            document.getElementById('historico').value = gato.historico || '';
            document.getElementById('ala_id').value = gato.ala_id || '';
            document.getElementById('doencas_pre_existentes').value = gato.doencas_pre_existentes || '';
            if (gato.foto) {
                document.getElementById('cat-photo-preview').innerHTML = `<img src="${gato.foto}" class="w-full h-full object-cover">`;
            } else {
                document.getElementById('cat-photo-preview').innerHTML = '<span class="text-xs text-gray-400">Sem foto</span>';
            }
        } else {
            document.getElementById('modalTitle').textContent = 'Cadastrar Gato';
            form.reset();
            document.getElementById('gato_id').value = '';
            document.getElementById('cat-photo-preview').innerHTML = '<span class="text-xs text-gray-400">Sem foto</span>';
        }
        document.getElementById('csrf_token').value = window.CSRF_TOKEN || '';
        
        el.classList.remove('hidden');
        el.classList.add('flex');
        setTimeout(() => {
            el.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 10);
    } else {
        // Close
        el.classList.add('opacity-0');
        content.classList.add('scale-95');
        setTimeout(() => {
            el.classList.add('hidden');
            el.classList.remove('flex');
        }, 300);
    }
}

async function loadGatos(page = 1) {
    const list = document.getElementById('gatos-list');
    const searchQuery = document.getElementById('search').value;
    
    list.innerHTML = Array(3).fill().map(() => `
        <div class="animate-pulse flex space-x-4 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
            <div class="rounded-full bg-slate-200 h-16 w-16"></div>
            <div class="flex-1 space-y-3 py-1">
                <div class="h-2 bg-slate-200 rounded"></div>
                <div class="space-y-3">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="h-2 bg-slate-200 rounded col-span-2"></div>
                        <div class="h-2 bg-slate-200 rounded col-span-1"></div>
                    </div>
                    <div class="h-2 bg-slate-200 rounded"></div>
                </div>
            </div>
        </div>
    `).join('');
    
    try {
        let url = `api/list_gatos.php?page=${page}`;
        if (searchQuery) url += `&search=${encodeURIComponent(searchQuery)}`;
        
        const res = await fetch(url);
        const json = await res.json();
        if (json.success) {
            gatosData = json.data.data;
            paginationData = json.data.pagination;
            renderGatos(gatosData);
            renderPagination();
        } else {
            AppUI.toast(json.message || 'Erro ao carregar', 'error');
            list.innerHTML = `<div class="text-center py-10 text-red-500">Erro ao carregar lista.</div>`;
        }
    } catch (e) {
        AppUI.toast('Erro de conexão', 'error');
        list.innerHTML = `<div class="text-center py-10 text-red-500">Erro de conexão.</div>`;
    }
}

function renderPagination() {
    const container = document.getElementById('pagination-container');
    if (!container || !paginationData || paginationData.total_pages <= 1) {
        if (container) container.innerHTML = '';
        return;
    }
    
    const { current_page, total_pages } = paginationData;
    const prevDisabled = current_page <= 1 ? 'opacity-50 cursor-not-allowed' : '';
    const nextDisabled = current_page >= total_pages ? 'opacity-50 cursor-not-allowed' : '';
    
    let pagesHtml = '';
    for (let i = Math.max(1, current_page - 2); i <= Math.min(total_pages, current_page + 2); i++) {
        const active = i === current_page ? 'bg-brand-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-50';
        pagesHtml += `<button onclick="loadGatos(${i})" class="w-10 h-10 rounded-lg flex items-center justify-center font-bold text-sm ${active}">${i}</button>`;
    }
    
    container.innerHTML = `
        <div class="flex justify-center items-center gap-2 mt-6">
            <button onclick="loadGatos(${current_page - 1})" class="w-10 h-10 rounded-lg flex items-center justify-center bg-white text-gray-600 hover:bg-gray-50 ${prevDisabled}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <div class="flex gap-1">${pagesHtml}</div>
            <button onclick="loadGatos(${current_page + 1})" class="w-10 h-10 rounded-lg flex items-center justify-center bg-white text-gray-600 hover:bg-gray-50 ${nextDisabled}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>
    `;
}

function previewCatPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('cat-photo-preview').innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function getStatusBadge(status) {
    const badges = {
        'ativo': '<span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-bold uppercase tracking-wider">Ativo</span>',
        'Doado': '<span class="px-2.5 py-1 bg-purple-100 text-purple-700 rounded-full text-[10px] font-bold uppercase tracking-wider">Doado</span>',
        'aposentado': '<span class="px-2.5 py-1 bg-blue-100 text-blue-700 rounded-full text-[10px] font-bold uppercase tracking-wider">Aposentado</span>',
        'óbito': '<span class="px-2.5 py-1 bg-red-100 text-red-700 rounded-full text-[10px] font-bold uppercase tracking-wider">Óbito</span>',
        // Fallbacks for legacy
        'residente': '<span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-bold uppercase tracking-wider">Ativo</span>',
        'tratamento': '<span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 rounded-full text-[10px] font-bold uppercase tracking-wider">Tratamento</span>',
        'adotado': '<span class="px-2.5 py-1 bg-purple-100 text-purple-700 rounded-full text-[10px] font-bold uppercase tracking-wider">Doado</span>',
    };
    return badges[status] || badges['ativo'];
}

async function loadAlasDropdown() {
    try {
        const res = await fetch('api/alas.php?action=list');
        const json = await res.json();
        if (json.success) {
            const select = document.getElementById('ala_id');
            // Keep first option
            select.innerHTML = '<option value="">Selecione uma Ala...</option>';
            json.data.forEach(ala => {
                const opt = new Option(ala.nome, ala.id);
                select.add(opt);
            });
        }
    } catch (e) {}
}

function renderGatos(gatos) {
    const list = document.getElementById('gatos-list');
    list.innerHTML = '';
    
    if (gatos.length === 0) {
        list.className = "";
        list.innerHTML = `
            <div class="text-center py-16 bg-white rounded-[2rem] border border-white shadow-premium">
                <div class="text-6xl mb-4">🐱</div>
                <p class="text-gray-800 font-display font-bold text-lg">Nenhum gato encontrado</p>
                <p class="text-gray-400 text-sm mt-2">Cadastre seu primeiro gato ou ajuste a busca</p>
                <button onclick="toggleModal('gatoModal')" class="mt-4 bg-brand-50 text-brand-600 px-6 py-2 rounded-full font-bold text-sm hover:bg-brand-100 transition-colors">Cadastrar</button>
            </div>`;
        return;
    }
    
    list.className = "grid grid-cols-1 md:grid-cols-2 gap-6";
    gatos.forEach(g => {
        const div = document.createElement('div');
        div.className = "premium-card bg-white p-5 rounded-[2rem] shadow-premium border border-white flex gap-5 items-center hover:shadow-xl transition-all cursor-pointer relative overflow-hidden group";
        
        const sexColor = g.sexo === 'M' ? 'bg-blue-400' : 'bg-pink-400';
        
        div.innerHTML = `
            <div class="absolute left-0 top-0 bottom-0 w-1.5 ${sexColor} opacity-70"></div>
            <div class="w-20 h-20 rounded-2xl bg-gray-50 flex-shrink-0 flex items-center justify-center overflow-hidden border-2 border-white shadow-inner group-hover:scale-105 transition-transform duration-500">
                ${g.foto ? `<img src="${g.foto}" class="w-full h-full object-cover">` : `<span class="text-3xl">${g.sexo === 'M' ? '😸' : '😺'}</span>`}
            </div>
            <div class="flex-1 min-w-0" onclick="window.location.href='perfil_gato.php?id=${g.id}'">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="text-xl font-display font-extrabold text-gray-800 truncate pr-2 tracking-tight">${g.nome}</h3>
                </div>
                <div class="flex flex-wrap text-xs text-gray-400 gap-3 items-center mb-3">
                    <span class="flex items-center font-medium"><svg class="w-3.5 h-3.5 mr-1 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> ${g.idade || 'N/I'}</span>
                    <span class="font-medium">${g.castrado == 1 ? '✂️ Castrado' : '⚠️ Intacto'}</span>
                </div>
                <div class="flex items-center gap-2">
                    ${getStatusBadge(g.status)}
                    ${g.ala_nome ? `<span class="bg-brand-50 text-brand-700 px-2 py-0.5 rounded-lg text-[10px] font-extrabold uppercase tracking-widest">📍 ${g.ala_nome}</span>` : ''}
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <button onclick="event.stopPropagation(); toggleModal('gatoModal', ${JSON.stringify(g).replace(/"/g, '&quot;')})" class="p-2.5 text-gray-300 hover:text-brand-500 hover:bg-brand-50 rounded-2xl transition-all active:scale-90">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                </button>
            </div>
        `;
        list.appendChild(div);
    });
}

let searchTimeout;
document.getElementById('search').addEventListener('input', (e) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => loadGatos(1), 300);
});

async function saveGato(e) {
    e.preventDefault();
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.innerText = 'Salvando...';
    AppUI.loading(true, 'Salvando...');
    
    const form = e.target;
    const formData = new FormData(form);
    formData.set('csrf_token', window.CSRF_TOKEN || '');
    
    const id = document.getElementById('gato_id').value;
    const endpoint = id ? 'api/update_gato.php' : 'api/create_gato.php';
    
    try {
        const res = await fetch(endpoint, { method: 'POST', body: formData });
        const json = await res.json();
        
        if (json.success) {
            toggleModal('gatoModal');
            loadGatos();
            AppUI.toast(json.message || 'Salvo com sucesso!', 'success');
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

// Check URL params
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('action') === 'new') {
    setTimeout(() => toggleModal('gatoModal'), 100);
}

document.addEventListener('DOMContentLoaded', () => {
    loadGatos();
    loadAlasDropdown();
});
</script>

<?php require_once __DIR__ . '/src/footer.php'; ?>
