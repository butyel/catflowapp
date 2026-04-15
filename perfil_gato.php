<?php
require_once __DIR__ . '/src/auth.php';
require_once __DIR__ . '/src/db.php';
require_login();

$gato_id = (int)($_GET['id'] ?? 0);
if (!$gato_id) {
    header('Location: gatos.php');
    exit;
}

// Fetch cat details
$stmt = $pdo->prepare("SELECT g.*, a.nome as ala_nome FROM gatos g LEFT JOIN alas a ON g.ala_id = a.id WHERE g.id = ?");
$stmt->execute([$gato_id]);
$gato = $stmt->fetch();

if (!$gato) {
    header('Location: gatos.php');
    exit;
}

$page_title = "Perfil: " . $gato['nome'];
require_once __DIR__ . '/src/header.php';
?>

<div class="space-y-8 -mt-10">
    <!-- Profile Header Card -->
    <div class="premium-card bg-white rounded-[2.5rem] shadow-premium border border-white p-8 relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-32 h-32 bg-brand-50 rounded-bl-[5rem] -mr-10 -mt-10 opacity-50 transition-transform group-hover:scale-110"></div>
        
        <div class="flex flex-col md:flex-row gap-8 items-center relative z-10">
            <div class="w-40 h-40 rounded-[2rem] border-4 border-white flex-shrink-0 relative overflow-hidden shadow-premium group-hover:rotate-2 transition-transform duration-500">
                <?php if ($gato['foto']): ?>
                    <img src="<?php echo $gato['foto']; ?>" class="w-full h-full object-cover">
                <?php
else: ?>
                    <div class="w-full h-full bg-gray-50 flex items-center justify-center text-6xl shadow-inner">
                        <?php echo $gato['sexo'] === 'M' ? '😸' : '😺'; ?>
                    </div>
                <?php
endif; ?>
            </div>
            
            <div class="flex-1 text-center md:text-left">
                <div class="flex flex-col md:flex-row md:items-center gap-3 mb-4">
                    <h1 class="text-4xl font-display font-extrabold text-gray-800 tracking-tight leading-none"><?php echo $gato['nome']; ?></h1>
                    <div class="flex gap-2 justify-center">
                        <?php
$statusClass = strtolower($gato['status']) === 'ativo' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-gray-50 text-gray-600 border-gray-100';
?>
                        <span class="px-3 py-1 <?php echo $statusClass; ?> rounded-full text-[10px] font-bold uppercase tracking-widest border">
                            <?php echo $gato['status']; ?>
                        </span>
                        <?php if ($gato['ala_nome']): ?>
                        <span class="px-3 py-1 bg-brand-50 text-brand-700 rounded-full text-[10px] font-bold uppercase tracking-widest border border-brand-100">
                            📍 <?php echo $gato['ala_nome']; ?>
                        </span>
                        <?php
endif; ?>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center md:text-left">
                    <div class="bg-gray-50/50 p-2 rounded-2xl border border-gray-100">
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Gênero</p>
                        <p class="text-sm font-bold text-gray-700"><?php echo $gato['sexo'] === 'M' ? 'Macho' : 'Fêmea'; ?></p>
                    </div>
                    <div class="bg-gray-50/50 p-2 rounded-2xl border border-gray-100">
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Idade</p>
                        <p class="text-sm font-bold text-gray-700"><?php echo $gato['idade'] ?: 'N/I'; ?></p>
                    </div>
                    <div class="bg-gray-50/50 p-2 rounded-2xl border border-gray-100">
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Peso</p>
                        <p class="text-sm font-bold text-gray-700"><?php echo($gato['peso'] ? $gato['peso'] . ' kg' : 'N/I'); ?></p>
                    </div>
                    <div class="bg-gray-50/50 p-2 rounded-2xl border border-gray-100">
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Raça</p>
                        <p class="text-sm font-bold text-gray-700 truncate"><?php echo $gato['raca'] ?: 'SDR'; ?></p>
                    </div>
                </div>

                <?php if ($gato['doencas_pre_existentes']): ?>
                <div class="mt-5 p-4 bg-red-50 border border-red-100 rounded-[1.5rem] flex items-start gap-3 shadow-sm">
                    <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center flex-shrink-0 shadow-sm">
                        <span class="text-red-500 text-xs">⚠️</span>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-red-600 uppercase tracking-widest">Cuidados Especiais / Alergias</p>
                        <p class="text-sm text-red-700 font-medium leading-relaxed"><?php echo nl2br($gato['doencas_pre_existentes']); ?></p>
                    </div>
                </div>
                <?php
endif; ?>
            </div>
        </div>
    </div>

    <!-- Tabbed Navigation -->
    <div class="flex p-1.5 bg-gray-100 rounded-2xl md:max-w-md mx-auto">
        <button onclick="switchTab('health')" id="tab-health-btn" class="flex-1 py-3 text-xs font-bold uppercase tracking-wider rounded-xl transition-all tab-btn active">Saúde</button>
        <button onclick="switchTab('routine')" id="tab-routine-btn" class="flex-1 py-3 text-xs font-bold uppercase tracking-wider rounded-xl transition-all tab-btn">Rotina</button>
        <button onclick="switchTab('gallery')" id="tab-gallery-btn" class="flex-1 py-3 text-xs font-bold uppercase tracking-wider rounded-xl transition-all tab-btn">Galeria</button>
    </div>

    <div id="tab-health" class="tab-content">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-4">
                <div class="flex justify-between items-center px-1">
                    <h2 class="text-xl font-display font-bold text-gray-800">Prontuário Médico</h2>
                    <button onclick="window.location.href='saude.php?gato_id=<?php echo $gato_id; ?>&nome=<?php echo urlencode($gato['nome']); ?>'" class="bg-brand-50 text-brand-600 px-4 py-2 rounded-full text-xs font-bold hover:bg-brand-100">Novo Registro</button>
                </div>
                <div id="health-timeline" class="bg-white rounded-[2rem] shadow-premium border border-white p-6 space-y-4 max-h-[500px] overflow-y-auto">
                    <p class="text-center text-gray-400 text-sm py-8 font-medium">Carregando histórico...</p>
                </div>
            </div>
            <div class="space-y-4">
                <h2 class="text-xl font-display font-bold text-gray-800 px-1">Evolução de Peso</h2>
                <div class="bg-white rounded-[2rem] shadow-premium border border-white p-6 h-[400px]">
                    <canvas id="weightChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div id="tab-routine" class="tab-content hidden">
        <div class="space-y-4 max-w-2xl mx-auto">
            <div class="flex justify-between items-center px-1">
                <h2 class="text-xl font-display font-bold text-gray-800">Monitoramento Diário</h2>
                <button onclick="toggleModal('monitoringModal')" class="bg-brand-500 text-white px-6 py-2.5 rounded-full text-xs font-bold shadow-brand active:scale-95 transition-all">Registrar Agora</button>
            </div>
            <div id="monitoring-list" class="grid grid-cols-1 gap-4">
                <p class="text-center text-gray-400 text-sm py-8 font-medium bg-white rounded-[2rem] shadow-premium border border-white">Carregando monitoramento...</p>
            </div>
        </div>
    </div>

    <div id="tab-gallery" class="tab-content hidden">
        <div class="space-y-6">
            <div class="flex justify-between items-center px-1">
                <h2 class="text-xl font-display font-bold text-gray-800">Diário Visual</h2>
                <button onclick="toggleModal('galleryModal')" class="bg-brand-500 text-white w-12 h-12 flex items-center justify-center rounded-2xl shadow-brand active:scale-95 transition-all">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                </button>
            </div>
            <div id="gallery-grid" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <p class="col-span-full text-center text-gray-400 text-sm py-8 font-medium bg-white rounded-[2rem] shadow-premium border border-white">Carregando galeria...</p>
            </div>
        </div>
    </div>
</div>

<style>
    .tab-btn.active { background: white; color: #14b8a6; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .tab-btn:not(.active) { color: #9ca3af; }
</style>

<!-- Modals -->
<!-- Monitoring Modal -->
<div id="monitoringModal" class="fixed inset-0 z-[60] bg-black/60 hidden items-center justify-center p-4 opacity-0 transition-opacity duration-300 backdrop-blur-sm">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md p-8 transform scale-95 transition-transform duration-300 overflow-y-auto max-h-[92vh] border border-gray-100" id="monitoringModalContent">
        <h2 class="text-2xl font-display font-extrabold text-gray-800 mb-8">Registrar Bem-estar</h2>
        <form id="monitoringForm" onsubmit="saveMonitoring(event)" class="space-y-6">
            <input type="hidden" name="gato_id" value="<?php echo $gato_id; ?>">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Apetite</label>
                    <select name="apetite" class="w-full px-4 py-3 rounded-2xl border border-gray-100 outline-none bg-gray-50 font-bold text-gray-700 focus:ring-4 focus:ring-brand-500/10 transition-all">
                        <option value="Normal">Normal</option>
                        <option value="Aumentado">Aumentado</option>
                        <option value="Reduzido">Reduzido</option>
                        <option value="Não comeu">Não comeu</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Energia</label>
                    <select name="energia" class="w-full px-4 py-3 rounded-2xl border border-gray-100 outline-none bg-gray-50 font-bold text-gray-700 focus:ring-4 focus:ring-brand-500/10 transition-all">
                        <option value="Normal">Normal</option>
                        <option value="Agitado">Agitado</option>
                        <option value="Lentificado">Lentificado</option>
                        <option value="Prostrado">Prostrado</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Caixa de Areia</label>
                    <select name="caixa_areia" class="w-full px-4 py-3 rounded-2xl border border-gray-100 outline-none bg-gray-50 font-bold text-gray-700 focus:ring-4 focus:ring-brand-500/10 transition-all">
                        <option value="Normal">Normal</option>
                        <option value="Diarreia">Diarreia</option>
                        <option value="Sem fezes">Sem fezes</option>
                        <option value="Sem urina">Sem urina</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Data</label>
                    <input type="date" name="data" value="<?php echo date('Y-m-d'); ?>" class="w-full px-4 py-3 rounded-2xl border border-gray-100 outline-none bg-gray-50 font-bold text-gray-700">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Observações</label>
                <textarea name="observacoes" rows="3" class="w-full px-4 py-3 rounded-2xl border border-gray-100 outline-none bg-gray-50 font-medium text-gray-700 resize-none focus:ring-4 focus:ring-brand-500/10 transition-all" placeholder="Como ele está se sentindo hoje?"></textarea>
            </div>
            
            <div class="p-4 bg-brand-50/50 rounded-2xl border border-brand-100/50">
                <label class="block text-[10px] font-bold text-brand-600 uppercase tracking-widest mb-1.5 ml-1">Peso Hoje (kg)</label>
                <input type="number" step="0.01" name="peso" placeholder="Opcional" class="w-full px-4 py-3 rounded-xl border-none outline-none bg-white font-bold text-brand-700 placeholder:text-brand-200">
            </div>

            <div class="flex gap-4 pt-4">
                <button type="button" onclick="toggleModal('monitoringModal')" class="flex-1 bg-gray-50 text-gray-500 py-4 rounded-2xl font-bold transition-all">Cancelar</button>
                <button type="submit" class="flex-1 bg-brand-500 text-white py-4 rounded-2xl font-bold shadow-brand active:scale-95 transition-all">Salvar Registro</button>
            </div>
        </form>
    </div>
</div>

<!-- Gallery Modal -->
<div id="galleryModal" class="fixed inset-0 z-[60] bg-black/50 hidden items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-xl w-full max-w-sm p-6 transform scale-95 transition-transform duration-300" id="galleryModalContent">
        <h2 class="text-xl font-bold text-gray-800 mb-5">Adicionar Foto</h2>
        <form id="galleryForm" onsubmit="saveGallery(event)" class="space-y-4">
            <input type="hidden" name="gato_id" value="<?php echo $gato_id; ?>">
            <input type="file" name="foto" required accept="image/*" class="w-full px-4 py-2 rounded-xl border border-gray-200 outline-none">
            <input type="text" name="legenda" placeholder="Legenda (opcional)" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none">
            <input type="date" name="data" value="<?php echo date('Y-m-d'); ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 outline-none">
            
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="toggleModal('galleryModal')" class="flex-1 bg-gray-100 text-gray-700 py-3 rounded-xl font-bold">Cancelar</button>
                <button type="submit" class="flex-1 bg-brand-500 text-white py-3 rounded-xl font-bold shadow-md shadow-brand-500/30">Postar</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const gatoId = <?php echo $gato_id; ?>;
let weightChart = null;

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

function switchTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    
    document.getElementById('tab-' + tab).classList.remove('hidden');
    document.getElementById('tab-' + tab + '-btn').classList.add('active');
}

async function loadHealth() {
    const res = await fetch(`api/list_saude.php?gato_id=${gatoId}`);
    const json = await res.json();
    const container = document.getElementById('health-timeline');
    container.innerHTML = json.data.length ? '' : '<p class="text-gray-400 text-center py-12 font-medium">Sem histórico médico registrado.</p>';
    
    json.data.forEach(h => {
        const item = document.createElement('div');
        item.className = "p-4 bg-gray-50/50 rounded-2xl border border-gray-100 flex gap-4 items-center transition-all hover:bg-white hover:shadow-premium";
        const icon = h.tipo === 'vacina' ? '💉' : (h.tipo === 'consulta' ? '🩺' : '📋');
        item.innerHTML = `
            <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center shadow-premium text-xl">${icon}</div>
            <div class="flex-1">
                <p class="text-sm font-bold text-gray-800">${h.descricao}</p>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">${h.data_evento}</p>
            </div>
        `;
        container.appendChild(item);
    });
}

async function loadMonitoring() {
    const res = await fetch(`api/monitoring.php?action=list&gato_id=${gatoId}`);
    const json = await res.json();
    const container = document.getElementById('monitoring-list');
    container.innerHTML = json.data.length ? '' : '<p class="text-gray-400 text-center py-12 font-medium bg-white rounded-[2rem] shadow-premium border border-white">Sem registros diários ainda.</p>';
    
    json.data.forEach(m => {
        const item = document.createElement('div');
        item.className = "premium-card bg-white p-6 rounded-[2rem] shadow-premium border border-white";
        item.innerHTML = `
            <div class="flex justify-between items-center mb-4">
                <div class="px-3 py-1 bg-brand-50 text-brand-600 rounded-full text-[10px] font-bold uppercase tracking-widest border border-brand-100">${m.data}</div>
            </div>
            <div class="grid grid-cols-3 gap-3 mb-4">
                <div class="bg-gray-50 p-2 rounded-xl text-center">
                    <p class="text-[9px] text-gray-400 font-bold uppercase mb-1">Apetite</p>
                    <p class="text-[11px] font-bold text-gray-700">🥣 ${m.apetite}</p>
                </div>
                <div class="bg-gray-50 p-2 rounded-xl text-center">
                    <p class="text-[9px] text-gray-400 font-bold uppercase mb-1">Energia</p>
                    <p class="text-[11px] font-bold text-gray-700">⚡ ${m.energia}</p>
                </div>
                <div class="bg-gray-50 p-2 rounded-xl text-center">
                    <p class="text-[9px] text-gray-400 font-bold uppercase mb-1">Caixa</p>
                    <p class="text-[11px] font-bold text-gray-700">📦 ${m.caixa_areia}</p>
                </div>
            </div>
            ${m.observacoes ? `<p class="text-sm text-gray-600 leading-relaxed italic bg-gray-50/50 p-3 rounded-xl border border-dashed border-gray-200">${m.observacoes}</p>` : ''}
        `;
        container.appendChild(item);
    });
}

async function loadGallery() {
    const res = await fetch(`api/galeria.php?action=list&gato_id=${gatoId}`);
    const json = await res.json();
    const container = document.getElementById('gallery-grid');
    container.innerHTML = json.data.length ? '' : '<p class="col-span-full text-gray-400 text-center py-4">Galeria vazia.</p>';
    
    json.data.forEach(img => {
        const div = document.createElement('div');
        div.className = "aspect-square rounded-2xl overflow-hidden border border-gray-100 shadow-sm";
        div.innerHTML = `<img src="${img.foto_path}" class="w-full h-full object-cover" title="${img.legenda || ''}">`;
        container.appendChild(div);
    });
}

async function loadWeight() {
    const res = await fetch(`api/peso.php?action=list&gato_id=${gatoId}`);
    const json = await res.json();
    if (!json.success || json.data.length === 0) return;

    const labels = json.data.map(d => d.data).reverse();
    const values = json.data.map(d => d.peso).reverse();

    const ctx = document.getElementById('weightChart').getContext('2d');
    if (weightChart) weightChart.destroy();
    weightChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Peso (kg)',
                data: values,
                borderColor: '#14b8a6',
                backgroundColor: 'rgba(20, 184, 166, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: false }, x: { grid: { display: false } } }
        }
    });
}

async function saveMonitoring(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    
    // Save monitoring
    const res = await fetch('api/monitoring.php?action=create', {
        method: 'POST', body: JSON.stringify(data)
    });
    
    // If peso provided, save it too
    if (data.peso) {
        await fetch('api/peso.php?action=create', {
            method: 'POST', body: JSON.stringify({gato_id: gatoId, peso: data.peso, data: data.data})
        });
    }

    toggleModal('monitoringModal');
    loadMonitoring();
    loadWeight();
    // Refresh page details to update peso badge
    setTimeout(() => window.location.reload(), 500);
}

async function saveGallery(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const res = await fetch('api/galeria.php?action=upload', {
        method: 'POST', body: formData
    });
    const json = await res.json();
    if (json.success) {
        toggleModal('galleryModal');
        loadGallery();
    } else alert(json.message);
}

document.addEventListener('DOMContentLoaded', () => {
    loadHealth();
    loadMonitoring();
    loadGallery();
    loadWeight();
});
</script>

<?php require_once __DIR__ . '/src/footer.php'; ?>
