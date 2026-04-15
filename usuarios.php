<?php
require_once __DIR__ . '/src/auth.php';
require_once __DIR__ . '/src/db.php';
require_login();
require_admin();

$page_title = "Gestão de Usuários";
require_once __DIR__ . '/src/header.php';

// Busca todos os usuários
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$usuarios = $stmt->fetchAll();
?>

<!-- Header Section -->
<div class="mb-8 -mt-10">
    <p class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-4 ml-1">Zona de Administração</p>
</div>

<div class="premium-card bg-white rounded-[2.5rem] shadow-premium border border-white overflow-hidden mb-8">
    <!-- Lista de usuários (Mobile First approach) -->
    <div class="divide-y divide-gray-50">
        <?php foreach ($usuarios as $u): ?>
        <div class="p-6 hover:bg-gray-50 flex items-center gap-5 transition-all group">
            <div class="w-14 h-14 bg-gray-50 text-gray-400 rounded-2xl flex items-center justify-center font-display font-black text-xl shadow-inner border-2 border-white group-hover:scale-105 transition-transform">
                <?php echo strtoupper(substr($u['nome'], 0, 1)); ?>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2 mb-1">
                    <h3 class="font-display font-bold text-gray-800 truncate tracking-tight text-lg"><?php echo htmlspecialchars($u['nome']); ?></h3>
                    <?php if ($u['role'] === 'admin'): ?>
                        <span class="px-2.5 py-0.5 rounded-lg text-[9px] uppercase font-black bg-purple-50 text-purple-600 border border-purple-100 tracking-widest">Admin</span>
                    <?php
    elseif ($u['role'] === 'ong'): ?>
                        <span class="px-2.5 py-0.5 rounded-lg text-[9px] uppercase font-black bg-blue-50 text-blue-600 border border-blue-100 tracking-widest">ONG</span>
                    <?php
    else: ?>
                        <span class="px-2.5 py-0.5 rounded-lg text-[9px] uppercase font-black bg-gray-50 text-gray-400 border border-gray-100 tracking-widest">Tutor</span>
                    <?php
    endif; ?>
                </div>
                <div class="flex items-center gap-3">
                    <p class="text-xs text-gray-400 font-medium truncate"><?php echo htmlspecialchars($u['email']); ?></p>
                    <span class="w-1 h-1 bg-gray-200 rounded-full"></span>
                    <p class="text-[10px] text-gray-300 font-bold uppercase tracking-tighter">Entrou em <?php echo date('M Y', strtotime($u['created_at'])); ?></p>
                </div>
            </div>
        </div>
        <?php
endforeach; ?>
        
        <?php if (count($usuarios) === 0): ?>
        <div class="p-8 text-center text-gray-500">Nenhum usuário encontrado.</div>
        <?php
endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/src/footer.php'; ?>
