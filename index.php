<?php
require_once __DIR__ . '/src/auth.php';

// If already logged in, redirect to dashboard
if (get_logged_user_id()) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>CATFLOW - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: { colors: { brand: { 500: '#14b8a6', 600: '#0d9488' } } }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #ccfbf1 0%, #f0fdfa 100%); }
        .glass-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="antialiased min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-extrabold text-brand-600 tracking-tight flex items-center justify-center gap-2">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"></path></svg>
                CATFLOW
            </h1>
            <p class="text-gray-500 mt-2 font-medium">Gestão inteligente para tutores e ONGs</p>
        </div>

        <div class="glass-card shadow-xl rounded-3xl p-8 border border-white/40">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 font-semibold">Bem-vindo(a) de volta!</h2>
            
            <form id="loginForm" class="space-y-6" onsubmit="handleLogin(event)">
                <div id="errorMsg" class="hidden bg-red-100 text-red-600 p-3 rounded-lg text-sm font-medium"></div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                    <input type="email" id="email" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 focus:ring focus:ring-brand-200 transition-all outline-none" placeholder="gato@exemplo.com">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                    <input type="password" id="senha" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 focus:ring focus:ring-brand-200 transition-all outline-none" placeholder="••••••••">
                </div>
                
                <button type="submit" id="submitBtn" class="w-full bg-brand-500 hover:bg-brand-600 text-white font-semibold py-3.5 rounded-xl shadow-md shadow-brand-500/30 transition-all transform active:scale-[0.98]">
                    Entrar no Sistema
                </button>
            </form>

            <div class="mt-8 space-y-4">
                <div class="relative flex items-center justify-center">
                    <span class="absolute inset-x-0 h-px bg-gray-200"></span>
                    <span class="relative bg-white px-4 text-sm text-gray-400 font-medium">Novo por aqui?</span>
                </div>
                
                <a href="register.php" class="flex w-full items-center justify-center gap-2 rounded-xl border-2 border-brand-500 bg-white py-3.5 text-brand-600 font-bold hover:bg-brand-50 transition-colors">
                    Criar Minha Conta
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                </a>
            </div>
        </div>
    </div>

    <script>
        async function handleLogin(e) {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            const errorMsg = document.getElementById('errorMsg');
            
            btn.disabled = true;
            btn.innerHTML = '<span class="animate-pulse">Entrando...</span>';
            errorMsg.classList.add('hidden');

            const payload = {
                email: document.getElementById('email').value,
                senha: document.getElementById('senha').value
            };

            try {
                const response = await fetch('api/login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                
                const text = await response.text();
                let data;
                
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    // Se não for JSON, o PHP falhou (Fatal error, sintaxe, etc)
                    throw new Error("Erro do Servidor: " + text.substring(0, 100)); 
                }
                
                if (data.success) {
                    window.location.href = 'dashboard.php';
                } else {
                    errorMsg.textContent = data.message;
                    errorMsg.classList.remove('hidden');
                    btn.disabled = false;
                    btn.innerHTML = 'Entrar no Sistema';
                }
            } catch (err) {
                errorMsg.textContent = err.message || 'Erro de conexão com o servidor.';
                errorMsg.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = 'Entrar no Sistema';
            }
        }
    </script>
</body>
</html>
