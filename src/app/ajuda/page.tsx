"use client";

import DashboardLayout from "@/components/DashboardLayout";

export default function AjudaPage() {
  return (
    <DashboardLayout title="Ajuda 📖">
      <div className="space-y-4 -mt-10">
        <FaqCard title="Como cadastrar um gato?" desc="Vá em Gatos > Novo Gato e preencha as informações básicas do felino." />
        <FaqCard title="Como registrar uma vacina?" desc="No perfil do gato, acesse a aba Saúde e clique em adicionar registro." />
        <FaqCard title="Como funciona o estoque?" desc="O estoque controla ração, areia e medicamentos. Você recebe alertas quando o estoque está baixo." />
        <FaqCard title="Posso exportar meus dados?" desc="Sim, vá em Configurações > Exportar Dados para gerar relatórios em CSV ou PDF." />
        <FaqCard title="Como funciona o financeiro?" desc="Registre receitas e despesas por categoria. O dashboard mostra um resumo mensal." />
        <FaqCard title="Precisa de mais ajuda?" desc="Entre em contato pelo suporte do CATFLOW para assistência personalizada." />
      </div>
    </DashboardLayout>
  );
}

function FaqCard({ title, desc }: { title: string; desc: string }) {
  return (
    <div className="bg-white rounded-2xl p-4 shadow-premium border border-white premium-card">
      <p className="font-bold text-gray-800">{title}</p>
      <p className="text-sm text-gray-500 mt-1">{desc}</p>
    </div>
  );
}
