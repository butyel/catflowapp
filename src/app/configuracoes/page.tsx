"use client";

import { useRouter } from "next/navigation";
import DashboardLayout from "@/components/DashboardLayout";

export default function ConfiguracoesPage() {
  const router = useRouter();

  return (
    <DashboardLayout title="Configurações ⚙️">
      <div className="space-y-4 -mt-10">
        <ConfigCard icon="🔔" title="Notificações" desc="Configurar lembretes e push notifications" />
        <ConfigCard icon="🎨" title="Aparência" desc="Tema e preferências visuais" />
        <ConfigCard icon="📤" title="Exportar Dados" desc="Exportar dados em CSV/PDF" />
        <ConfigCard icon="💾" title="Backup" desc="Fazer backup do banco de dados" />
      </div>
    </DashboardLayout>
  );
}

function ConfigCard({ icon, title, desc }: { icon: string; title: string; desc: string }) {
  return (
    <button className="w-full bg-white rounded-2xl p-4 shadow-premium border border-white premium-card text-left">
      <div className="flex items-center gap-4">
        <span className="text-2xl">{icon}</span>
        <div>
          <p className="font-bold text-gray-800">{title}</p>
          <p className="text-xs text-gray-400">{desc}</p>
        </div>
      </div>
    </button>
  );
}
