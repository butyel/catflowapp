<?php
require_once __DIR__ . '/src/auth.php';
require_login();
$page_title = "Olá, " . explode(' ', $_SESSION['user_nome'])[0] . " 👋";
require_once __DIR__ . '/src/header.php';
?>

<div class="space-y-8 -mt-10">
    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 gap-4">
        <div class="premium-card bg-white rounded-3xl p-5 shadow-premium border border-white flex flex-col items-center justify-center text-center">
            <div class="w-14 h-14 bg-brand-50 text-brand-500 rounded-2xl flex items-center justify-center mb-3 shadow-inner">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"></path></svg>
            </div>
            <h3 class="text-3xl font-display font-extrabold text-gray-800" id="total-gatos">-</h3>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Gatos Ativos</p>
        </div>
        <div class="premium-card bg-white rounded-3xl p-5 shadow-premium border border-white flex flex-col items-center justify-center text-center">
            <div class="w-14 h-14 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center mb-3 shadow-inner">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
            </div>
            <h3 class="text-3xl font-display font-extrabold text-gray-800" id="gatos-tratamento">-</h3>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Tratamentos</p>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 text-center">
            <p class="text-xl font-extrabold text-green-500" id="stat-medicamentos">-</p>
            <p class="text-[9px] text-gray-400 font-bold uppercase">Medicamentos</p>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 text-center">
            <p class="text-xl font-extrabold text-purple-500" id="stat-estoque">-</p>
            <p class="text-[9px] text-gray-400 font-bold uppercase">Estoque Baixo</p>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 text-center">
            <p class="text-xl font-extrabold text-yellow-500" id="stat-vacinas">-</p>
            <p class="text-[9px] text-gray-400 font-bold uppercase">Vacinas Pend.</p>
        </div>
    </div>

    <!-- Alertas Fixos (Medicamentos/Vacinas hoje) -->
    <div id="alertas-container" class="space-y-4 hidden">
        <h2 class="text-xl font-display font-bold text-gray-800 px-1">Aproximando agora... ⚠️</h2>
        <div id="alertas-list" class="space-y-2"></div>
    </div>

    <!-- Gráfico / Visão Financeira -->
    <div class="bg-white rounded-[2rem] shadow-premium p-6 border border-white">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-display font-bold text-gray-800">Financeiro <span class="text-gray-400 font-medium text-sm">(<?php echo date('M'); ?>)</span></h2>
            <a href="financeiro.php" class="bg-brand-50 text-brand-600 px-4 py-1.5 rounded-full text-xs font-bold hover:bg-brand-100 transition-colors">Ver tudo</a>
        </div>
        
        <div class="flex justify-between mb-6">
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Entradas</p>
                <p class="text-2xl font-display font-extrabold text-green-500" id="total-receitas">R$ 0,00</p>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Saídas</p>
                <p class="text-2xl font-display font-extrabold text-red-500" id="total-despesas">R$ 0,00</p>
            </div>
        </div>

        <div class="w-full bg-gray-50 rounded-full h-4 mb-1 overflow-hidden flex border border-gray-100 p-0.5">
            <div id="bar-receita" class="bg-green-400 h-full rounded-full transition-all duration-1000 shadow-[0_0_10px_rgba(74,222,128,0.3)]" style="width: 50%"></div>
            <div id="bar-despesa" class="bg-red-400 h-full rounded-full transition-all duration-1000 shadow-[0_0_10px_rgba(248,113,113,0.3)]" style="width: 50%"></div>
        </div>
        
        <div class="flex justify-between mt-3 text-xs text-gray-400">
            <span id="pct-receita">--%</span>
            <span id="pct-despesa">--%</span>
        </div>
    </div>

    <!-- Gestão do Gatil Section -->
    <div class="space-y-4">
        <h2 class="text-xl font-display font-bold text-gray-800 px-1">Gestão do Gatil</h2>
        <div class="grid grid-cols-3 gap-3 md:gap-4">
            <button onclick="window.location.href='alas.php'" class="premium-card bg-white rounded-2xl md:rounded-3xl p-3 md:p-5 border border-white flex flex-col items-center justify-center shadow-premium group">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-blue-50 text-blue-500 rounded-xl md:rounded-2xl flex items-center justify-center mb-2 md:mb-3 group-hover:scale-110 transition-transform shadow-inner">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <span class="text-[8px] md:text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Alas</span>
            </button>
            <button onclick="window.location.href='estoque.php'" class="premium-card bg-white rounded-2xl md:rounded-3xl p-3 md:p-5 border border-white flex flex-col items-center justify-center shadow-premium group">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-green-50 text-green-500 rounded-xl md:rounded-2xl flex items-center justify-center mb-2 md:mb-3 group-hover:scale-110 transition-transform shadow-inner">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <span class="text-[8px] md:text-[10px] font-extrabold text-gray-400 uppercase tracking-widest text-center">Estoque</span>
            </button>
            <button onclick="window.location.href='dicas.php'" class="premium-card bg-white rounded-2xl md:rounded-3xl p-3 md:p-5 border border-white flex flex-col items-center justify-center shadow-premium group">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-orange-50 text-orange-500 rounded-xl md:rounded-2xl flex items-center justify-center mb-2 md:mb-3 group-hover:scale-110 transition-transform shadow-inner">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.364-6.364l-.707-.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M12 12a3 3 0 100-6 3 3 0 000 6z"></path></svg>
                </div>
                <span class="text-[8px] md:text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Dicas</span>
            </button>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-3 gap-3 md:gap-4 mb-10 pt-4">
        <button onclick="window.location.href='gatos.php?action=new'" class="bg-brand-500 text-white rounded-[1.5rem] md:rounded-[2rem] p-3 md:p-5 flex flex-col items-center justify-center transition-all active:scale-95 shadow-brand">
            <svg class="w-6 h-6 md:w-7 md:h-7 mb-1 md:mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            <span class="text-[8px] md:text-[10px] font-bold uppercase tracking-wider">Novo Gato</span>
        </button>
        <button onclick="window.location.href='financeiro.php?action=despesa'" class="bg-white text-red-500 rounded-[1.5rem] md:rounded-[2rem] p-3 md:p-5 flex flex-col items-center justify-center transition-all active:scale-95 shadow-premium border border-red-50">
            <svg class="w-6 h-6 md:w-7 md:h-7 mb-1 md:mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="text-[8px] md:text-[10px] font-bold uppercase tracking-wider">Despesa</span>
        </button>
        <button onclick="window.location.href='adocoes.php?action=new'" class="bg-white text-purple-600 rounded-[1.5rem] md:rounded-[2rem] p-3 md:p-5 flex flex-col items-center justify-center transition-all active:scale-95 shadow-premium border border-purple-50">
            <svg class="w-6 h-6 md:w-7 md:h-7 mb-1 md:mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
            <span class="text-[8px] md:text-[10px] font-bold uppercase tracking-wider">Adoção</span>
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    try {
        AppUI.loading(true, 'Carregando...');
        const res = await fetch('api/dashboard_stats.php');
        const json = await res.json();
        
        if (json.success) {
            const { stats, alerts } = json.data;
            
            document.getElementById('total-gatos').textContent = stats.gatos_ativos;
            document.getElementById('gatos-tratamento').textContent = stats.em_tratamento;
            document.getElementById('stat-medicamentos').textContent = stats.medicamentos_ativos;
            document.getElementById('stat-estoque').textContent = stats.estoque_baixo;
            document.getElementById('stat-vacinas').textContent = stats.vacinas_pendentes;
            
            document.getElementById('total-receitas').textContent = `R$ ${stats.total_receitas.toFixed(2).replace('.', ',')}`;
            document.getElementById('total-despesas').textContent = `R$ ${stats.total_despesas.toFixed(2).replace('.', ',')}`;
            
            const max = Math.max(stats.total_receitas + stats.total_despesas, 1);
            const rPct = (stats.total_receitas / max) * 100;
            const dPct = (stats.total_despesas / max) * 100;
            
            document.getElementById('bar-receita').style.width = `${rPct}%`;
            document.getElementById('bar-despesa').style.width = `${dPct}%`;
            document.getElementById('pct-receita').textContent = rPct.toFixed(0) + '%';
            document.getElementById('pct-despesa').textContent = dPct.toFixed(0) + '%';
            
            if (alerts && alerts.length > 0) {
                const container = document.getElementById('alertas-container');
                const list = document.getElementById('alertas-list');
                container.classList.remove('hidden');
                list.innerHTML = '';
                
                const severityStyles = {
                    yellow: 'bg-yellow-50 border-yellow-400 text-yellow-800 ring-yellow-100',
                    blue: 'bg-blue-50 border-blue-400 text-blue-800 ring-blue-100',
                    red: 'bg-red-50 border-red-400 text-red-800 ring-red-100',
                    purple: 'bg-purple-50 border-purple-400 text-purple-800 ring-purple-100'
                };
                
                alerts.forEach(alert => {
                    const el = document.createElement('div');
                    el.className = `flex items-center gap-4 p-4 border-l-4 rounded-xl shadow-sm ring-1 ring-inset ${severityStyles[alert.severity] || severityStyles.blue} transition-all hover:shadow-md`;
                    el.innerHTML = `
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <p class="text-[10px] font-black uppercase tracking-widest opacity-60">${alert.title}</p>
                                ${alert.isToday ? '<span class="text-[8px] font-black bg-white/50 px-1.5 py-0.5 rounded uppercase animate-pulse">Hoje</span>' : ''}
                            </div>
                            <p class="text-sm font-bold leading-tight">${alert.message}</p>
                            ${alert.date ? `<p class="text-[10px] mt-1 font-bold opacity-60">${alert.date}</p>` : ''}
                        </div>
                    `;
                    list.appendChild(el);
                });
            }
        }
    } catch (err) {
        console.error("Erro ao carregar dashboard", err);
        AppUI.toast('Erro ao carregar dados', 'error');
    } finally {
        AppUI.loading(false);
    }
});
</script>

<?php if (is_admin()): ?>
<div class="bg-white rounded-[2rem] shadow-premium p-6 border border-white mt-8">
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
            </div>
            <h2 class="text-lg font-display font-bold text-gray-800">Administração</h2>
        </div>
    </div>
    
    <div class="grid grid-cols-3 gap-4">
        <button onclick="exportData()" class="flex flex-col items-center gap-2 p-4 bg-green-50 hover:bg-green-100 rounded-2xl transition-colors">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span class="text-xs font-bold text-green-700">Exportar Dados</span>
        </button>
        <button onclick="createBackup()" class="flex flex-col items-center gap-2 p-4 bg-blue-50 hover:bg-blue-100 rounded-2xl transition-colors">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
            <span class="text-xs font-bold text-blue-700">Criar Backup</span>
        </button>
        <button onclick="sendNotification()" class="flex flex-col items-center gap-2 p-4 bg-orange-50 hover:bg-orange-100 rounded-2xl transition-colors">
            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            <span class="text-xs font-bold text-orange-700">Enviar Notificação</span>
        </button>
    </div>
    
    <div class="mt-6 pt-6 border-t border-gray-100">
        <h3 class="text-sm font-bold text-gray-600 mb-4 uppercase tracking-wider">Backup Automático</h3>
        <p class="text-xs text-gray-400 mb-3">O sistema mantém os últimos 10 backups. Backups são armazenados em /backups/</p>
    </div>
</div>

<script>
async function exportData() {
    const type = prompt('Tipo de exportação:\n- gatos\n- financeiro\n- saude\n- adocoes');
    if (!type) return;
    const format = prompt('Formato: csv ou pdf') || 'csv';
    
    try {
        const res = await fetch('api/export.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({type, format})
        });
        const json = await res.json();
        if (json.success) {
            window.open(json.file, '_blank');
            AppUI.toast('Exportado com sucesso!', 'success');
        } else {
            AppUI.toast(json.message, 'error');
        }
    } catch (e) {
        AppUI.toast('Erro ao exportar', 'error');
    }
}

async function createBackup() {
    AppUI.loading(true);
    try {
        const res = await fetch('api/backup.php', {method: 'POST'});
        const json = await res.json();
        if (json.success) {
            AppUI.toast('Backup criado!', 'success');
        } else {
            AppUI.toast(json.message, 'error');
        }
    } catch (e) {
        AppUI.toast('Erro ao criar backup', 'error');
    } finally {
        AppUI.loading(false);
    }
}

async function sendNotification() {
    const title = prompt('Título da notificação:');
    if (!title) return;
    const body = prompt('Mensagem:');
    if (!body) return;
    
    try {
        const res = await fetch('api/send_push.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({title, body})
        });
        const json = await res.json();
        if (json.success) {
            AppUI.toast('Enviada para ' + json.recipients + ' usuários', 'success');
        } else {
            AppUI.toast(json.message, 'error');
        }
    } catch (e) {
        AppUI.toast('Erro ao enviar', 'error');
    }
}
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/src/footer.php'; ?>
