<?php
require_once __DIR__ . '/src/auth.php';
require_login();

$page_title = "Central de Ajuda";
require_once __DIR__ . '/src/header.php';
?>

<div class="space-y-6 -mt-4">
    <!-- Branding Card -->
    <div class="bg-brand-900 rounded-3xl p-8 text-center text-white shadow-xl relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-10">
            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"></path></svg>
        </div>
        
        <div class="relative z-10">
            <div class="w-20 h-20 bg-white/20 rounded-2xl mx-auto flex items-center justify-center mb-4 backdrop-blur-md border border-white/30">
                <span class="text-3xl">🛡️</span>
            </div>
            <h2 class="text-2xl font-black mb-1">CATFLOW</h2>
            <div class="inline-block bg-brand-500 text-[10px] font-black uppercase tracking-[0.2em] px-3 py-1 rounded-full mb-6">Versão 1.0 Complete</div>
            
            <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-4 border border-white/10">
                <p class="text-sm font-medium text-brand-100 mb-1">Criador e CEO</p>
                <p class="text-xl font-bold">Raphael Fernandes</p>
            </div>
        </div>
    </div>

    <!-- Contact Options -->
    <div class="grid grid-cols-1 gap-4">
        <a href="https://wa.me/5518981939533" target="_blank" class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-5 hover:bg-green-50 transition-colors group">
            <div class="w-14 h-14 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.72.937 3.659 1.432 5.626 1.433h.004c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-800">WhatsApp Suporte</p>
                <p class="text-xs text-gray-500">(18) 98193-9533</p>
            </div>
        </a>

        <a href="mailto:butyel95@gmail.com" class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-5 hover:bg-blue-50 transition-colors group">
            <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-800">Email Direto</p>
                <p class="text-xs text-gray-500">butyel95@gmail.com</p>
            </div>
        </a>
    </div>

    <div class="bg-gray-50 border border-gray-100 rounded-2xl p-6 text-center">
        <p class="text-xs text-gray-400 font-medium">© 2026 CATFLOW. Desenvolvido com ❤️ por Raphael Fernandes para cuidadores de gatos extraordinários.</p>
    </div>
</div>

<?php require_once __DIR__ . '/src/footer.php'; ?>
