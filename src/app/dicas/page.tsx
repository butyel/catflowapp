"use client";

import DashboardLayout from "@/components/DashboardLayout";

const dicas = [
  { icon: "🏥", title: "Check-ups Regulares", desc: "Leve seus gatos ao veterinário pelo menos uma vez por ano para check-ups de rotina." },
  { icon: "💊", title: "Medicação em Dia", desc: "Mantenha um calendário de medicamentos e vacinas para não perder nenhuma dose." },
  { icon: "🍽️", title: "Alimentação", desc: "Ofereça ração de qualidade e mantenha água fresca sempre disponível." },
  { icon: "🧹", title: "Higiene", desc: "Mantenha as caixas de areia sempre limpas para evitar doenças." },
  { icon: "❤️", title: "Socialização", desc: "Gatos socializados são mais saudáveis e felizes. Dedique tempo para brincar." },
  { icon: "📋", title: "Registros", desc: "Mantenha o CATFLOW sempre atualizado com o histórico de cada gato." },
];

export default function DicasPage() {
  return (
    <DashboardLayout title="Dicas 📚">
      <div className="space-y-4 -mt-10">
        {dicas.map((dica, i) => (
          <div key={i} className="bg-white rounded-2xl p-4 shadow-premium border border-white premium-card">
            <div className="flex gap-4">
              <span className="text-2xl">{dica.icon}</span>
              <div>
                <p className="font-bold text-gray-800">{dica.title}</p>
                <p className="text-sm text-gray-500 mt-1">{dica.desc}</p>
              </div>
            </div>
          </div>
        ))}
      </div>
    </DashboardLayout>
  );
}
