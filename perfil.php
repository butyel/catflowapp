<?php
require_once __DIR__ . '/src/auth.php';
require_login();

// Logout Action
if (isset($_GET['logout'])) {
    logout_user();
}

$page_title = "Meu Perfil";
require_once __DIR__ . '/src/header.php';
?>

<div class="premium-card bg-white rounded-[2.5rem] shadow-premium border border-white p-8 mb-8 text-center -mt-10 relative overflow-hidden group">
    <div class="absolute top-0 left-0 w-32 h-32 bg-brand-50 rounded-br-[5rem] -ml-10 -mt-10 opacity-30"></div>
    
    <div class="relative inline-block group mb-4">
        <div id="profile-img-container" class="w-32 h-32 bg-brand-50 text-brand-500 rounded-[2.5rem] mx-auto flex items-center justify-center text-4xl font-display font-extrabold shadow-inner border-4 border-white overflow-hidden group-hover:rotate-3 transition-transform duration-500">
            <?php if (isset($_SESSION['user_foto']) && $_SESSION['user_foto']): ?>
                <img src="<?php echo $_SESSION['user_foto']; ?>" class="w-full h-full object-cover" id="current-photo">
            <?php
else: ?>
                <span id="photo-placeholder"><?php echo strtoupper(substr($_SESSION['user_nome'], 0, 1)); ?></span>
            <?php
endif; ?>
        </div>
        <button onclick="document.getElementById('photo-input').click()" class="absolute -bottom-2 -right-2 bg-brand-500 text-white p-3 rounded-2xl shadow-brand hover:bg-brand-600 transition-all active:scale-90 border-4 border-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
        </button>
        <input type="file" id="photo-input" class="hidden" accept="image/*" onchange="uploadPhoto(this)">
    </div>
    
    <h2 class="text-2xl font-display font-black text-gray-800 tracking-tight leading-none"><?php echo htmlspecialchars($_SESSION['user_nome']); ?></h2>
    <p class="text-[10px] text-brand-600 mt-2 uppercase tracking-[0.2em] font-black"><?php echo htmlspecialchars($_SESSION['user_role']); ?></p>
</div>

<script>
async function uploadPhoto(input) {
    if (!input.files || !input.files[0]) return;
    
    const formData = new FormData();
    formData.append('foto', input.files[0]);
    
    const container = document.getElementById('profile-img-container');
    container.classList.add('animate-pulse', 'opacity-50');
    
    try {
        const res = await fetch('api/update_profile_photo.php', {
            method: 'POST',
            body: formData
        });
        const json = await res.json();
        
        if (json.success) {
            container.innerHTML = `<img src="${json.path}?t=${new Date().getTime()}" class="w-full h-full object-cover" id="current-photo">`;
            AppUI.toast('Foto atualizada!', 'success');
        } else {
            AppUI.toast(json.message || 'Erro ao enviar', 'error');
        }
    } catch (e) {
        AppUI.toast('Erro ao enviar foto', 'error');
    } finally {
        container.classList.remove('animate-pulse', 'opacity-50');
    }
}
</script>

<div class="bg-white rounded-[2.5rem] shadow-premium border border-white overflow-hidden mb-8">
    <div class="border-b border-gray-50 p-6 flex justify-between items-center cursor-pointer hover:bg-gray-50 transition-all group" onclick="window.location.href='configuracoes.php'">
        <div class="flex items-center gap-4">
            <span class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </span>
            <span class="font-bold text-gray-700">Configurações da Conta</span>
        </div>
        <svg class="w-6 h-6 text-gray-300 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
    </div>
    <div class="border-b border-gray-50 p-6 flex justify-between items-center cursor-pointer hover:bg-gray-50 transition-all group" onclick="window.location.href='ajuda.php'">
        <div class="flex items-center gap-4">
            <span class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </span>
            <span class="font-bold text-gray-700">Central de Ajuda</span>
        </div>
        <svg class="w-6 h-6 text-gray-300 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
    </div>
    <div class="border-b border-gray-50 p-6 flex justify-between items-center cursor-pointer hover:bg-gray-50 transition-all group" onclick="window.location.href='perfil.php?push=1'">
        <div class="flex items-center gap-4">
            <span class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            </span>
            <span class="font-bold text-gray-700">Notificações Push</span>
        </div>
        <svg class="w-6 h-6 text-gray-300 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
    </div>
    <?php if (is_admin()): ?>
    <div class="p-6 flex justify-between items-center cursor-pointer hover:bg-purple-50 transition-all group bg-purple-50/30" onclick="window.location.href='usuarios.php'">
        <div class="flex items-center gap-4">
            <span class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </span>
            <span class="font-black text-purple-800 uppercase text-xs tracking-widest">Gerenciar Usuários</span>
        </div>
        <svg class="w-6 h-6 text-purple-300 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
    </div>
    <?php
endif; ?>
</div>

<a href="perfil.php?logout=true" class="w-full flex items-center justify-center gap-3 bg-red-50 hover:bg-red-100 text-red-600 font-black py-5 rounded-[2rem] transition-all active:scale-95 text-xs uppercase tracking-[0.2em] shadow-sm">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
    Sair da Conta
</a>

<?php require_once __DIR__ . '/src/footer.php'; ?>
