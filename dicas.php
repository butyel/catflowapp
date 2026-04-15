<?php
require_once __DIR__ . '/src/auth.php';
require_login();
$page_title = "Dicas e Comportamento";
require_once __DIR__ . '/src/header.php';
?>

<div class="space-y-6 -mt-4">
    <div class="bg-brand-500 rounded-3xl p-8 text-white shadow-lg overflow-hidden relative">
        <div class="relative z-10">
            <h1 class="text-3xl font-black mb-2">Manual do Bem-estar Felino</h1>
            <p class="text-brand-100 max-w-md">Dicas práticas para manter seus gatinhos saudáveis, felizes e estimulados.</p>
        </div>
        <div class="absolute right-0 bottom-0 text-9xl opacity-20 pointer-events-none transform translate-y-4 translate-x-4">🐱</div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Tip Card 1 -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-green-100 rounded-2xl flex items-center justify-center text-2xl mb-4">🩺</div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">Saúde Preventiva</h2>
            <ul class="text-sm text-gray-600 space-y-2 list-disc list-inside">
                <li>Check-ups anuais são essenciais para detecção precoce de doenças.</li>
                <li>Mantenha as vacinas V4/V5 e Antirrábica em dia.</li>
                <li>A desparasitação (vermes e pulgas) deve ser periódica.</li>
            </ul>
        </div>

        <!-- Tip Card 2 -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-orange-100 rounded-2xl flex items-center justify-center text-2xl mb-4">🧶</div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">Enriquecimento Ambiental</h2>
            <ul class="text-sm text-gray-600 space-y-2 list-disc list-inside">
                <li>Gatos precisam de prateleiras e lugares altos para se sentirem seguros.</li>
                <li>Brincadeiras de caça reduzem o estresse e a obesidade.</li>
                <li>Arranhadores são fundamentais para a saúde física e mental.</li>
            </ul>
        </div>

        <!-- Tip Card 3 -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center text-2xl mb-4">💧</div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">Hidratação e Nutrição</h2>
            <ul class="text-sm text-gray-600 space-y-2 list-disc list-inside">
                <li>Fontes de água corrente incentivam o gato a beber mais água.</li>
                <li>Ofereça alimentação úmida (sachê) diariamente.</li>
                <li>Mantenha as tigelas de água longe da caixa de areia e da comida.</li>
            </ul>
        </div>

        <!-- Tip Card 4 -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-purple-100 rounded-2xl flex items-center justify-center text-2xl mb-4">🛋️</div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">Comportamento Social</h2>
            <ul class="text-sm text-gray-600 space-y-2 list-disc list-inside">
                <li>Respeite o espaço do gato; eles gostam de momentos de solidão.</li>
                <li>Mudanças bruscas na rotina podem causar estresse e problemas de saúde.</li>
                <li>O ronrom nem sempre é felicidade; pode ser um sinal de dor ou pacificação.</li>
            </ul>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/src/footer.php'; ?>
