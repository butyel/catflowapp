<?php
require_once __DIR__ . '/src/auth.php';
require_once __DIR__ . '/src/db.php';
require_login();

$user_id = get_logged_user_id();
$stmt = $pdo->prepare("SELECT nome, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$page_title = "Configurações da Conta";
require_once __DIR__ . '/src/header.php';
?>

<div class="premium-card bg-white rounded-[2.5rem] shadow-premium border border-white p-8 -mt-10 overflow-hidden relative">
    <div class="absolute top-0 right-0 w-32 h-32 bg-brand-50 rounded-bl-[5rem] -mr-10 -mt-10 opacity-30"></div>
    
    <form id="settingsForm" onsubmit="saveSettings(event)" class="space-y-6 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Nome Completo</label>
                <input type="text" name="nome" value="<?php echo htmlspecialchars($user['nome']); ?>" required class="w-full px-5 py-4 rounded-2xl border border-gray-100 outline-none bg-gray-50 font-bold text-gray-700 focus:ring-4 focus:ring-brand-500/10 transition-all">
            </div>
            
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Email de Acesso</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="w-full px-5 py-4 rounded-2xl border border-gray-100 outline-none bg-gray-50 font-bold text-gray-700 focus:ring-4 focus:ring-brand-500/10 transition-all">
            </div>
        </div>
        
        <div class="p-6 bg-brand-50/30 rounded-3xl border border-brand-100/50 mt-4">
            <label class="block text-[10px] font-bold text-brand-600 uppercase tracking-widest mb-3 ml-1">Segurança & Senha</label>
            <div class="relative">
                <input type="password" name="password" placeholder="Mudar senha (opcional)" class="w-full px-5 py-4 rounded-2xl border-none outline-none bg-white font-bold text-gray-700 placeholder:text-gray-300 focus:ring-4 focus:ring-brand-500/10 transition-all">
                <p class="text-[10px] text-brand-400 mt-2 ml-1 font-medium">Deixe em branco para manter a senha atual.</p>
            </div>
        </div>
        
        <div class="pt-4">
            <button type="submit" id="saveBtn" class="w-full bg-brand-500 text-white font-display font-black text-xs uppercase tracking-[0.2em] py-5 rounded-[2rem] shadow-brand hover:bg-brand-600 transition-all active:scale-95">
                Salvar Alterações
            </button>
        </div>
    </form>
</div>

<script>
async function saveSettings(e) {
    e.preventDefault();
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.innerText = 'Salvando...';
    
    const formData = new FormData(e.target);
    const payload = Object.fromEntries(formData.entries());
    
    try {
        const res = await fetch('api/update_account.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        
        if (json.success) {
            alert('Perfil atualizado com sucesso!');
            window.location.reload();
        } else {
            alert(json.message);
        }
    } catch (err) {
        console.error('Settings Error:', err);
        alert('Erro ao salvar alterações: ' + err.message);
    } finally {
        btn.disabled = false;
        btn.innerText = 'Salvar Alterações';
    }
}
</script>

<?php require_once __DIR__ . '/src/footer.php'; ?>
