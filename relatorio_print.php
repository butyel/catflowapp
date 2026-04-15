<?php
// relatorio_print.php
require_once __DIR__ . '/src/db.php';
require_once __DIR__ . '/src/auth.php';
require_once __DIR__ . '/src/utils.php';

require_login();

$user_id = get_logged_user_id();
$mes = isset($_GET['mes']) ? (int)$_GET['mes'] : date('m');
$ano = isset($_GET['ano']) ? (int)$_GET['ano'] : date('Y');

try {
    $stmt = $pdo->prepare("SELECT * FROM financeiro WHERE user_id = ? AND MONTH(data) = ? AND YEAR(data) = ? ORDER BY data ASC");
    $stmt->execute([$user_id, $mes, $ano]);
    $registros = $stmt->fetchAll();

    $total_receitas = 0;
    $total_despesas = 0;
    foreach ($registros as $r) {
        if ($r['tipo'] === 'receita')
            $total_receitas += $r['valor'];
        else
            $total_despesas += $r['valor'];
    }
    $saldo = $total_receitas - $total_despesas;
}
catch (Exception $e) {
    die("Erro ao carregar relatório.");
}

$meses = ["", "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório Financeiro - <?php echo $meses[$mes] . "/" . $ano; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { background: white; padding: 0; }
            .print-container { width: 100%; border: none; shadow: none; }
        }
    </style>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white p-10 shadow-sm border border-gray-200 print-container">
        <div class="flex justify-between items-center mb-8 border-b pb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">CATFLOW</h1>
                <p class="text-gray-500 text-sm uppercase tracking-wider">Relatório Financeiro Mensal</p>
            </div>
            <div class="text-right">
                <p class="font-bold text-lg"><?php echo $meses[$mes] . " / " . $ano; ?></p>
                <p class="text-xs text-gray-400">Gerado em: <?php echo date('d/m/Y H:i'); ?></p>
            </div>
        </div>

        <!-- Resumo -->
        <div class="grid grid-cols-3 gap-6 mb-10">
            <div class="p-4 bg-green-50 rounded-xl border border-green-100">
                <p class="text-xs font-bold text-green-600 uppercase mb-1">Total Receitas</p>
                <p class="text-xl font-bold text-green-700">R$ <?php echo number_format($total_receitas, 2, ',', '.'); ?></p>
            </div>
            <div class="p-4 bg-red-50 rounded-xl border border-red-100">
                <p class="text-xs font-bold text-red-600 uppercase mb-1">Total Despesas</p>
                <p class="text-xl font-bold text-red-700">R$ <?php echo number_format($total_despesas, 2, ',', '.'); ?></p>
            </div>
            <div class="p-4 <?php echo $saldo >= 0 ? 'bg-blue-50 border-blue-100' : 'bg-orange-50 border-orange-100'; ?> rounded-xl border">
                <p class="text-xs font-bold <?php echo $saldo >= 0 ? 'text-blue-600' : 'text-orange-600'; ?> uppercase mb-1">Saldo Final</p>
                <p class="text-xl font-bold <?php echo $saldo >= 0 ? 'text-blue-700' : 'text-orange-700'; ?>">R$ <?php echo number_format($saldo, 2, ',', '.'); ?></p>
            </div>
        </div>

        <!-- Tabela -->
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase">Data</th>
                    <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase">Descrição</th>
                    <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase">Categoria</th>
                    <th class="py-3 px-4 text-xs font-bold text-gray-500 uppercase text-right">Valor</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($registros as $r): ?>
                <tr class="border-b border-gray-100 last:border-none">
                    <td class="py-3 px-4 text-sm"><?php echo date('d/m', strtotime($r['data'])); ?></td>
                    <td class="py-3 px-4 text-sm font-medium"><?php echo $r['descricao']; ?></td>
                    <td class="py-3 px-4 text-xs text-gray-500 capitalize"><?php echo $r['categoria']; ?></td>
                    <td class="py-3 px-4 text-sm font-bold text-right <?php echo $r['tipo'] === 'receita' ? 'text-green-600' : 'text-red-600'; ?>">
                        <?php echo($r['tipo'] === 'receita' ? '+' : '-') . ' R$ ' . number_format($r['valor'], 2, ',', '.'); ?>
                    </td>
                </tr>
                <?php
endforeach; ?>
                <?php if (empty($registros)): ?>
                    <tr><td colspan="4" class="py-8 text-center text-gray-400 text-sm">Nenhum registro encontrado.</td></tr>
                <?php
endif; ?>
            </tbody>
        </table>

        <div class="mt-12 pt-8 border-t text-center text-gray-400 text-xs italic no-print">
            Dica: No diálogo de impressão, selecione "Salvar como PDF".
        </div>

        <div class="fixed bottom-6 right-6 no-print">
            <button onclick="window.print()" class="bg-brand-500 hover:bg-brand-600 text-white font-bold py-3 px-6 rounded-full shadow-lg transition-transform active:scale-95">
                Imprimir / Gerar PDF
            </button>
        </div>
    </div>

    <script>
        // Auto-print if param present
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('autoprint')) {
            setTimeout(() => window.print(), 500);
        }
    </script>
</body>
</html>
