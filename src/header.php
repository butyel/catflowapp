<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/alerts.php';
require_once __DIR__ . '/UI.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>CATFLOW</title>
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            500: '#14b8a6', // Teal 500
                            600: '#0d9488',
                            900: '#134e4a',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    },
                    boxShadow: {
                        'premium': '0 10px 30px -5px rgba(0, 0, 0, 0.05), 0 8px 15px -8px rgba(0, 0, 0, 0.05)',
                        'brand': '0 10px 20px -5px rgba(20, 184, 166, 0.3)',
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#14b8a6">
    <link rel="apple-touch-icon" href="assets/icons/icon-192x192.png">
    <script>const CSRF_TOKEN = '<?php echo $_SESSION['csrf_token']; ?>';
    
    const PushManager = {
        vapidPublicKey: null,
        
        async init(vapidKey) {
            this.vapidPublicKey = vapidKey;
            if (!('Notification' in window) || !('serviceWorker' in navigator)) return;
            
            if (Notification.permission === 'granted') {
                await this.subscribe();
            }
        },
        
        async subscribe() {
            if (!this.vapidPublicKey) return;
            
            try {
                const reg = await navigator.serviceWorker.ready;
                const sub = await reg.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: this.urlBase64ToUint8Array(this.vapidPublicKey)
                });
                
                await fetch('api/push.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(sub.toJSON())
                });
            } catch (e) {
                console.log('Push subscription failed:', e);
            }
        },
        
        urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }
    };
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #0f172a; padding-bottom: 80px; letter-spacing: -0.01em; }
        .font-display { font-family: 'Outfit', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.75); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-top: 1px solid rgba(255, 255, 255, 0.3); }
        .glass-dark { background: rgba(20, 184, 166, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
        .fade-in { animation: fadeIn 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        
        .premium-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .premium-card:active { transform: scale(0.98); }
        
        ::-webkit-scrollbar { width: 0px; background: transparent; }
        
        .animate-bounce-in { animation: bounceIn 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55); }
        @keyframes bounceIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        
        .shimmer { background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; }
        @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }
    </style>
</head>
<body class="antialiased min-h-screen relative pb-20">
    <div id="alerts-container">
        <?php display_flash_message(); ?>
    </div>
    
    <!-- Top Header Layout for Pages -->
    <?php if (isset($page_title)): ?>
    <header class="bg-gradient-to-br from-brand-500 to-brand-600 text-white shadow-brand rounded-b-[2.5rem] pt-10 pb-14 px-6 mb-[-40px] relative z-0">
        <div class="absolute top-10 right-6">
            <button onclick="toggleLembretes()" class="relative p-2.5 bg-white/20 backdrop-blur-md rounded-2xl hover:bg-white/30 transition-all active:scale-90 border border-white/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                <span id="notif-badge" class="hidden absolute top-0.5 right-0.5 w-3.5 h-3.5 bg-red-500 border-2 border-brand-500 rounded-full animate-bounce"></span>
            </button>
        </div>
        <h1 class="text-3xl font-display font-extrabold tracking-tight text-center leading-tight"><?php echo $page_title; ?></h1>
    </header>

    <!-- Modal Lembretes -->
    <div id="notifModal" class="fixed inset-0 z-[70] bg-black/50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="p-6 border-b flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">Lembretes 🔔</h2>
                <button onclick="toggleLembretes()" class="text-gray-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div id="lembretes-list" class="max-h-96 overflow-y-auto p-4 space-y-3">
                <p class="text-center text-gray-500 text-sm py-4">Carregando...</p>
            </div>
            <div class="p-4 bg-gray-50 text-center">
                <button onclick="window.location.href='dashboard.php'" class="text-brand-600 font-bold text-sm">Ver todos no Dashboard</button>
            </div>
        </div>
    </div>

    <script>
    function toggleLembretes() {
        const modal = document.getElementById('notifModal');
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            loadLembretes();
        } else {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    async function loadLembretes() {
        const list = document.getElementById('lembretes-list');
        try {
            const res = await fetch('api/list_lembretes.php');
            const json = await res.json();
            
            if (json.success && json.data.length > 0) {
                list.innerHTML = json.data.map(l => `
                    <div class="p-3 bg-brand-50 rounded-xl border border-brand-100">
                        <p class="font-bold text-gray-800 text-sm">${l.titulo}</p>
                        <p class="text-xs text-gray-500">${l.descricao || ''}</p>
                        <p class="text-[10px] text-brand-600 font-bold mt-1 uppercase">${l.data_lembrete}</p>
                    </div>
                `).join('');
                document.getElementById('notif-badge').classList.remove('hidden');
            } else {
                list.innerHTML = '<p class="text-center text-gray-400 text-sm py-8">Nenhum lembrete pendente.</p>';
                document.getElementById('notif-badge').classList.add('hidden');
            }
        } catch (e) {
            list.innerHTML = '<p class="text-center text-red-400 text-sm py-8">Erro ao carregar lembretes.</p>';
        }
    }
    // Check for notifications on load
    setTimeout(loadLembretes, 1000);
    </script>
    <?php
endif; ?>

    <main class="container mx-auto px-4 relative z-10 fade-in">
