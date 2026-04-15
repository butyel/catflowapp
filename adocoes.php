<?php
require_once __DIR__ . '/src/auth.php';
require_login();
$page_title = "Adoções";
require_once __DIR__ . '/src/header.php';
?>

<div class="flex justify-between items-center mb-6 -mt-4">
    <h2 class="text-lg font-bold text-gray-800">Histórico de Adoções</h2>
    <button onclick="toggleModal('adocaoModal')" class="bg-purple-500 hover:bg-purple-600 text-white rounded-xl p-2 w-10 h-10 flex items-center justify-center shadow-md">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
    </button>
</div>

<div id="adocoes-list" class="space-y-4">
    <div class="text-center py-6 text-gray-500 text-sm">Carregando...</div>
</div>

<!-- Add Modal -->
<div id="adocaoModal" class="fixed inset-0 z-[60] bg-black/50 hidden items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-xl w-full max-w-md" id="adocaoModalContent">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-5">Registrar Adoção</h2>
            <form id="adocaoForm" onsubmit="saveAdocao(event)" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gato *</label>
                    <select id="modal_gato_id" name="gato_id" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none">
                        <!-- Populated via JS -->
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Adotante *</label>
                    <input type="text" id="adotante_nome" name="adotante_nome" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contato (Telefone/Email) *</label>
                    <input type="text" id="contato" name="contato" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data da Adoção *</label>
                    <input type="date" id="data_adocao" name="data_adocao" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observações (Opcional)</label>
                    <textarea id="observacoes" name="observacoes" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none"></textarea>
                </div>
                <div class="pt-2 flex gap-3">
                    <button type="button" onclick="toggleModal('adocaoModal')" class="flex-1 bg-gray-100 text-gray-700 font-semibold py-3 rounded-xl">Cancelar</button>
                    <button type="submit" id="saveBtn" class="flex-1 bg-brand-500 text-white font-semibold py-3 rounded-xl shadow-md">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleModal(modalID) {
    const el = document.getElementById(modalID);
    if (el.classList.contains('hidden')) {
        document.getElementById('adocaoForm').reset();
        document.getElementById('data_adocao').value = new Date().toISOString().split('T')[0];
        el.classList.remove('hidden'); el.classList.add('flex');
        setTimeout(() => el.classList.remove('opacity-0'), 10);
    } else {
        el.classList.add('opacity-0');
        setTimeout(() => { el.classList.add('hidden'); el.classList.remove('flex'); }, 300);
    }
}

async function loadFormGatos() {
    try {
        const res = await fetch('api/list_gatos.php');
        const json = await res.json();
        if (json.success) {
            const select = document.getElementById('modal_gato_id');
            select.innerHTML = '';
            // Only resident cats can be adopted
            json.data.filter(g => g.status !== 'adotado').forEach(g => {
                select.add(new Option(g.nome, g.id));
            });
            if (select.options.length === 0) {
                select.add(new Option("Sem gatos disponíveis para adoção", ""));
            }
        }
    } catch(e) {}
}

async function loadAdocoes() {
    try {
        const res = await fetch('api/list_adocoes.php');
        const json = await res.json();
        const list = document.getElementById('adocoes-list');
        list.innerHTML = '';
        
        if (json.success) {
            if (json.data.length === 0) {
                list.innerHTML = `<div class="text-center py-10 bg-white rounded-xl shadow-sm border border-gray-100 outline-none"><p class="text-4xl mb-2">🎉</p><p class="text-gray-500">Nenhuma adoção registrada ainda.</p></div>`;
                return;
            }

            json.data.forEach(a => {
                const el = document.createElement('div');
                el.className = "bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex gap-4";
                const dateStr = a.data_adocao.split('-').reverse().join('/');
                
                el.innerHTML = `
                    <div class="w-14 h-14 rounded-full flex-shrink-0 flex items-center justify-center overflow-hidden bg-purple-100 text-purple-500 text-2xl">
                        ${a.gato_foto ? `<img src="${a.gato_foto}" class="w-full h-full object-cover">` : '🐱'}
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <h3 class="font-bold text-gray-800">${a.gato_nome}</h3>
                            <span class="text-xs font-bold text-purple-600 bg-purple-50 px-2 py-1 rounded">Adopted</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">Adotante: <span class="font-semibold">${a.adotante_nome}</span></p>
                        <p class="text-xs text-gray-400 mt-1">Data: ${dateStr}</p>
                    </div>
                `;
                list.appendChild(el);
            });
        }
    } catch(e) {}
}

async function saveAdocao(e) {
    e.preventDefault();
    const btn = document.getElementById('saveBtn'); btn.disabled = true;
    
    // Check if empty choice
    if (!document.getElementById('modal_gato_id').value) {
        alert('Nenhum gato selecionado válido.');
        btn.disabled = false;
        return;
    }

    const payload = Object.fromEntries(new FormData(e.target).entries());
    
    try {
        const res = await fetch('api/create_adocao.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        
        if (json.success) {
            toggleModal('adocaoModal');
            loadAdocoes();
            loadFormGatos(); // Refresh available cats
        } else alert(json.message);
    } catch (err) {} finally { btn.disabled = false; }
}

const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('action') === 'new') {
    setTimeout(() => toggleModal('adocaoModal'), 100);
}

document.addEventListener('DOMContentLoaded', () => {
    loadAdocoes();
    loadFormGatos();
});
</script>

<?php require_once __DIR__ . '/src/footer.php'; ?>
