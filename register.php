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
    <title>CATFLOW - Cadastro</title>
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
<body class="antialiased min-h-screen flex items-center justify-center p-4 py-10">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-brand-600 tracking-tight flex items-center justify-center gap-2">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"></path></svg>
                Criar Conta
            </h1>
            <p class="text-gray-500 mt-2 font-medium">Junte-se à revolução na gestão felina</p>
        </div>

        <div class="glass-card shadow-xl rounded-3xl p-8 border border-white/40">
            <form id="registerForm" class="space-y-5" onsubmit="handleRegister(event)">
                <div id="errorMsg" class="hidden bg-red-100 text-red-600 p-3 rounded-lg text-sm font-medium"></div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
                    <input type="text" id="nome" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 focus:ring focus:ring-brand-200 transition-all outline-none" placeholder="Seu nome">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                    <input type="email" id="email" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 focus:ring focus:ring-brand-200 transition-all outline-none" placeholder="gato@exemplo.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                    <input type="password" id="senha" required minlength="6" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 focus:ring focus:ring-brand-200 transition-all outline-none" placeholder="••••••••">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Conta</label>
                    <select id="role" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 focus:ring focus:ring-brand-200 transition-all outline-none bg-white">
                        <option value="tutor">Tutor de Gatos</option>
                        <option value="ong">Protetor Independente / ONG</option>
                    </select>
                </div>
                
                <button type="submit" id="submitBtn" class="w-full bg-brand-500 hover:bg-brand-600 text-white font-semibold py-3.5 rounded-xl shadow-md shadow-brand-500/30 transition-all transform active:scale-[0.98] mt-2">
                    Criar Minha Conta
                </button>
            </form>

            <p class="text-center text-gray-500 mt-6 text-sm">
                Já possui uma conta? <a href="index.php" class="text-brand-600 font-semibold hover:underline">Fazer login</a>
            </p>
        </div>
    </div>

    <script>
        async function handleRegister(e) {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            const errorMsg = document.getElementById('errorMsg');
            
            btn.disabled = true;
            btn.innerHTML = '<span class="animate-pulse">Cadastrando...</span>';
            errorMsg.classList.add('hidden');

            const payload = {
                nome: document.getElementById('nome').value,
                email: document.getElementById('email').value,
                senha: document.getElementById('senha').value,
                role: document.getElementById('role').value
            };

            try {
                const response = await fetch('api/register.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                
                const text = await response.text();
                let data;
                
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    throw new Error("Erro do Servidor: " + text.substring(0, 100)); 
                }
                
                if (data.success) {
                    window.location.href = 'dashboard.php';
                } else {
                    errorMsg.textContent = data.message;
                    errorMsg.classList.remove('hidden');
                    btn.disabled = false;
                    btn.innerHTML = 'Criar Minha Conta';
                }
            } catch (err) {
                errorMsg.textContent = err.message || 'Erro de conexão com o servidor.';
                errorMsg.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = 'Criar Minha Conta';
            }
        }
    </script>
</body>
</html>
