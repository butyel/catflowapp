<?php
require_once __DIR__ . '/src/auth.php';
require_login();
$page_title = "Gestão de Alas";
require_once __DIR__ . '/src/header.php';
?>

<div class="flex justify-between items-center mb-6 -mt-4">
    <div class="flex items-center gap-3">
        <a href="dashboard.php" class="bg-white p-2 rounded-xl text-gray-400 hover:text-brand-500 shadow-sm border border-gray-100 transition-all active:scale-90">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Alas do Gatil</h1>
    </div>
    <button onclick="toggleModal('alaModal')" class="bg-brand-500 hover:bg-brand-600 text-white rounded-xl px-4 py-2 font-bold shadow-md flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Nova Ala
    </button>
</div>

<div id="alas-list" class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <!-- Alas populated via JS -->
    <div class="animate-pulse bg-white p-6 rounded-2xl shadow-sm border border-gray-100 h-32"></div>
</div>

<!-- Ala Modal -->
<div id="alaModal" class="fixed inset-0 z-[60] bg-black/50 hidden items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-xl w-full max-w-md p-6 transform scale-95 transition-transform duration-300" id="alaModalContent">
        <h2 class="text-xl font-bold text-gray-800 mb-5">Cadastrar Ala</h2>
        <form id="alaForm" onsubmit="saveAla(event)" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome da Ala *</label>
                <input type="text" name="nome" required placeholder="Ex: Ala Sul, Berçário" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                <textarea name="descricao" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none resize-none" placeholder="Opcional..."></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="toggleModal('alaModal')" class="flex-1 bg-gray-100 text-gray-700 py-3 rounded-xl font-bold font-bold">Cancelar</button>
                <button type="submit" class="flex-1 bg-brand-500 text-white py-3 rounded-xl font-bold shadow-md shadow-brand-500/30">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
async function loadAlas() {
    const res = await fetch('api/alas.php?action=list');
    const json = await res.json();
    const list = document.getElementById('alas-list');
    list.innerHTML = '';
    
    if (json.data.length === 0) {
        list.innerHTML = `
            <div class="col-span-full text-center py-10 bg-white rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-gray-500">Nenhuma ala cadastrada. Crie uma para organizar seus gatos.</p>
            </div>`;
    }

    json.data.forEach(ala => {
        const div = document.createElement('div');
        div.className = "bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative group";
        div.innerHTML = `
            <h3 class="text-lg font-bold text-gray-800 mb-1">${ala.nome}</h3>
            <p class="text-sm text-gray-500 line-clamp-2">${ala.descricao || 'Sem descrição'}</p>
            <button onclick="deleteAla(${ala.id})" class="absolute top-4 right-4 text-gray-300 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
        `;
        list.appendChild(div);
    });
}

function toggleModal(id) {
    const el = document.getElementById(id);
    const content = document.getElementById(id + 'Content');
    if (el.classList.contains('hidden')) {
        el.classList.replace('hidden', 'flex');
        setTimeout(() => { el.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
    } else {
        el.classList.add('opacity-0'); content.classList.add('scale-95');
        setTimeout(() => el.classList.replace('flex', 'hidden'), 300);
    }
}

async function saveAla(e) {
    e.preventDefault();
    const payload = Object.fromEntries(new FormData(e.target));
    const res = await fetch('api/alas.php?action=create', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(payload)
    });
    const json = await res.json();
    if (json.success) {
        toggleModal('alaModal');
        loadAlas();
    } else alert(json.message);
}

async function deleteAla(id) {
    if (!confirm('Excluir esta ala? Isso não removerá os gatos, eles ficarão sem ala.')) return;
    const res = await fetch('api/alas.php?action=delete', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({id})
    });
    loadAlas();
}

document.addEventListener('DOMContentLoaded', loadAlas);
</script>

<?php require_once __DIR__ . '/src/header.php'; ?>
