"use client";

import { useState, useEffect } from "react";
import { useRouter } from "next/navigation";
import DashboardLayout from "@/components/DashboardLayout";

type Medicamento = {
  id: number;
  gatoId: number;
  nomeMedicamento: string;
  dosagem: string;
  horario: string;
  duracaoDias: number;
  status: string;
  gato: { id: number; nome: string };
};

export default function MedicamentosPage() {
  const router = useRouter();
  const [meds, setMeds] = useState<Medicamento[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetch("/api/medicamentos")
      .then((r) => r.json())
      .then((json) => {
        if (json.success) setMeds(json.data);
      })
      .finally(() => setLoading(false));
  }, []);

  const ativos = meds.filter((m) => m.status === "ativo");

  return (
    <DashboardLayout title="Medicamentos 💊">
      <div className="space-y-6 -mt-10">
        <a
          href="/gatos"
          className="inline-block bg-brand-500 text-white px-6 py-3 rounded-xl font-bold hover:bg-brand-600 transition-all shadow-lg shadow-brand-200"
        >
          + Novo Medicamento (em Gatos)
        </a>

        {loading ? (
          <div className="space-y-3">
            {[1, 2, 3].map((i) => (
              <div key={i} className="shimmer h-20 rounded-2xl" />
            ))}
          </div>
        ) : ativos.length === 0 ? (
          <div className="text-center py-16">
            <p className="text-gray-400">Nenhum medicamento ativo</p>
          </div>
        ) : (
          <div className="space-y-3">
            {ativos.map((m) => (
              <button
                key={m.id}
                onClick={() => router.push(`/gatos/${m.gatoId}`)}
                className="w-full bg-white rounded-2xl p-4 shadow-premium border border-white text-left premium-card"
              >
                <div className="flex justify-between items-start">
                  <div>
                    <p className="font-bold text-gray-800">{m.nomeMedicamento}</p>
                    <p className="text-xs text-gray-500">{m.dosagem} - {m.gato.nome}</p>
                  </div>
                  <div className="text-right">
                    <span className="text-[10px] font-bold px-2.5 py-1 rounded-full bg-brand-50 text-brand-600">
                      {m.horario}h
                    </span>
                    <p className="text-[10px] text-gray-400 mt-1">{m.duracaoDias}d</p>
                  </div>
                </div>
              </button>
            ))}
          </div>
        )}
      </div>
    </DashboardLayout>
  );
}
