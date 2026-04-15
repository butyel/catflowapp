    </main>
    
    <?php if (get_logged_user_id()): ?>
    <!-- Bottom Navigation Bar (Mobile-first App Style) -->
    <nav class="fixed bottom-0 left-0 w-full glass z-50 px-2 pb-safe border-none shadow-[0_-1px_20px_rgba(0,0,0,0.05)]">
        <div class="flex justify-around items-center h-18 max-w-lg mx-auto py-2">
            <a href="dashboard.php" class="flex flex-col items-center justify-center w-full transition-all duration-300 active:scale-90 <?php echo strpos($_SERVER['PHP_SELF'], 'dashboard') !== false ? 'text-brand-600' : 'text-gray-400'; ?>">
                <div class="relative p-1.5 <?php echo strpos($_SERVER['PHP_SELF'], 'dashboard') !== false ? 'bg-brand-50 rounded-xl' : ''; ?>">
                    <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider">Home</span>
            </a>
            <a href="gatos.php" class="flex flex-col items-center justify-center w-full transition-all duration-300 active:scale-90 <?php echo strpos($_SERVER['PHP_SELF'], 'gato') !== false ? 'text-brand-600' : 'text-gray-400'; ?>">
                <div class="relative p-1.5 <?php echo strpos($_SERVER['PHP_SELF'], 'gato') !== false ? 'bg-brand-50 rounded-xl' : ''; ?>">
                    <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"></path></svg>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider">Gatos</span>
            </a>
            <a href="saude.php" class="flex flex-col items-center justify-center w-full transition-all duration-300 active:scale-90 <?php echo strpos($_SERVER['PHP_SELF'], 'saude') !== false || strpos($_SERVER['PHP_SELF'], 'medicamentos') !== false ? 'text-brand-600' : 'text-gray-400'; ?>">
                <div class="relative p-1.5 <?php echo strpos($_SERVER['PHP_SELF'], 'saude') !== false || strpos($_SERVER['PHP_SELF'], 'medicamentos') !== false ? 'bg-brand-50 rounded-xl' : ''; ?>">
                    <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider">Saúde</span>
            </a>
            <a href="financeiro.php" class="flex flex-col items-center justify-center w-full transition-all duration-300 active:scale-90 <?php echo strpos($_SERVER['PHP_SELF'], 'financeiro') !== false ? 'text-brand-600' : 'text-gray-400'; ?>">
                <div class="relative p-1.5 <?php echo strpos($_SERVER['PHP_SELF'], 'financeiro') !== false ? 'bg-brand-50 rounded-xl' : ''; ?>">
                    <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider">Finanças</span>
            </a>
            <a href="perfil.php" class="flex flex-col items-center justify-center w-full transition-all duration-300 active:scale-90 <?php echo strpos($_SERVER['PHP_SELF'], 'perfil') !== false ? 'text-brand-600' : 'text-gray-400'; ?>">
                <div class="relative p-1.5 <?php echo strpos($_SERVER['PHP_SELF'], 'perfil') !== false ? 'bg-brand-50 rounded-xl' : ''; ?>">
                    <?php if (isset($_SESSION['user_foto']) && $_SESSION['user_foto']): ?>
                        <div class="w-6 h-6 rounded-full overflow-hidden border-2 <?php echo strpos($_SERVER['PHP_SELF'], 'perfil') !== false ? 'border-brand-500' : 'border-gray-300'; ?>">
                            <img src="<?php echo $_SESSION['user_foto']; ?>" class="w-full h-full object-cover">
                        </div>
                    <?php
    else: ?>
                        <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <?php
    endif; ?>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider">Perfil</span>
            </a>
        </div>
    </nav>
    <?php
endif; ?>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('service-worker.js')
                    .then(reg => console.log('Service Worker registered'))
                    .catch(err => console.log('Service Worker registration failed:', err));
            });
        }

        const AppUI = {
            toast(message, type = 'success', duration = 3000) {
                const icons = {
                    success: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
                    error: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
                    warning: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
                    info: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                };
                const colors = { success: 'bg-green-500', error: 'bg-red-500', warning: 'bg-yellow-500', info: 'bg-blue-500' };
                
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 left-1/2 -translate-x-1/2 z-[100] flex items-center gap-3 px-4 py-3 rounded-2xl text-white shadow-lg transform transition-all duration-300 -translate-y-20 opacity-0';
                toast.id = 'toast-' + Date.now();
                toast.innerHTML = `<span class="flex-shrink-0">${icons[type] || icons.info}</span><span class="font-semibold text-sm">${message}</span>`;
                toast.style.backgroundColor = type === 'success' ? '#22c55e' : type === 'error' ? '#ef4444' : type === 'warning' ? '#eab308' : '#3b82f6';
                
                document.body.appendChild(toast);
                setTimeout(() => toast.classList.remove('-translate-y-20', 'opacity-0'), 10);
                setTimeout(() => {
                    toast.classList.add('-translate-y-20', 'opacity-0');
                    setTimeout(() => toast.remove(), 300);
                }, duration);
            },

            loading(show = true, message = 'Carregando...') {
                let overlay = document.getElementById('app-loading-overlay');
                if (show) {
                    if (!overlay) {
                        overlay = document.createElement('div');
                        overlay.id = 'app-loading-overlay';
                        overlay.className = 'fixed inset-0 bg-black/30 backdrop-blur-sm z-[90] hidden items-center justify-center';
                        overlay.innerHTML = `<div class="bg-white rounded-2xl p-6 shadow-2xl flex flex-col items-center gap-3"><div class="w-10 h-10 animate-spin rounded-full border-4 border-gray-100 border-t-brand-500"></div><span class="text-sm font-semibold text-gray-600">${message}</span></div>`;
                        document.body.appendChild(overlay);
                    }
                    overlay.classList.remove('hidden');
                    overlay.classList.add('flex');
                } else {
                    if (overlay) {
                        overlay.classList.add('hidden');
                        overlay.classList.remove('flex');
                    }
                }
            },

            confirm(message, onConfirm, title = 'Confirmar') {
                return new Promise((resolve) => {
                    const modal = document.createElement('div');
                    modal.className = 'fixed inset-0 z-[80] bg-black/50 hidden items-center justify-center p-4 backdrop-blur-sm';
                    modal.innerHTML = `<div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-sm overflow-hidden animate-bounce-in"><div class="p-6 text-center"><svg class="w-12 h-12 mx-auto text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg><h3 class="text-xl font-display font-extrabold text-gray-800 mb-2">${title}</h3><p class="text-gray-500 text-sm mb-6">${message}</p><div class="flex gap-3"><button id="confirmCancel" class="flex-1 bg-gray-100 text-gray-600 font-bold py-3 rounded-xl hover:bg-gray-200 transition-colors">Cancelar</button><button id="confirmOk" class="flex-1 bg-red-500 text-white font-bold py-3 rounded-xl hover:bg-red-600 transition-colors">Confirmar</button></div></div></div>`;
                    document.body.appendChild(modal);
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    
                    document.getElementById('confirmCancel').onclick = () => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                        setTimeout(() => modal.remove(), 300);
                        resolve(false);
                    };
                    document.getElementById('confirmOk').onclick = () => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                        setTimeout(() => modal.remove(), 300);
                        if (onConfirm) onConfirm();
                        resolve(true);
                    };
                });
            },

            async api(url, options = {}) {
                const defaultOptions = {
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN || '' },
                };
                const mergedOptions = { ...defaultOptions, ...options };
                if (mergedOptions.body && typeof mergedOptions.body === 'object' && !(mergedOptions.body instanceof FormData)) {
                    mergedOptions.body = JSON.stringify(mergedOptions.body);
                }
                try {
                    const res = await fetch(url, mergedOptions);
                    const data = await res.json();
                    if (!data.success && res.status === 429) {
                        this.toast(data.message || 'Muitas tentativas. Tente novamente.', 'warning');
                        throw new Error(data.message);
                    }
                    return data;
                } catch (err) {
                    if (err.message !== 'Many attempts') {
                        this.toast('Erro de conexão', 'error');
                    }
                    throw err;
                }
            }
        };
    </script>
</body>
</html>
