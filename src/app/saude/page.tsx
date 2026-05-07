"use client";

import { useState, useEffect } from "react";
import { useRouter } from "next/navigation";
import DashboardLayout from "@/components/DashboardLayout";

type SaudeRecord = {
  id: number;
  gatoId: number;
  tipo: string;
  descricao: string;
  dataEvento: string;
  proximaData: string | null;
  gato: { id: number; nome: string };
};

export default function SaudePage() {
  const router = useRouter();
  const [records, setRecords] = useState<SaudeRecord[]>([]);
  const [loading, setLoading] = useState(true);
  const [filter, setFilter] = useState("");

  useEffect(() => {
    fetch("/api/saude")
      .then((r) => r.json())
      .then((json) => {
        if (json.success) setRecords(json.data);
      })
      .finally(() => setLoading(false));
  }, []);

  const filtered = filter
    ? records.filter((r) => r.gato.nome.toLowerCase().includes(filter.toLowerCase()) || r.descricao.toLowerCase().includes(filter.toLowerCase()))
    : records;

  function formatDate(d: string) {
    return new Date(d).toLocaleDateString("pt-BR");
  }

  return (
    <DashboardLayout title="Saúde 🏥">
      <div className="space-y-6 -mt-10">
        <input
          type="text"
          value={filter}
          onChange={(e) => setFilter(e.target.value)}
          placeholder="Filtrar por gato ou descrição..."
          className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none transition-all bg-white"
        />

        {loading ? (
          <div className="space-y-3">
            {[1, 2, 3].map((i) => (
              <div key={i} className="shimmer h-20 rounded-2xl" />
            ))}
          </div>
        ) : filtered.length === 0 ? (
          <div className="text-center py-16">
            <p className="text-gray-400">Nenhum registro de saúde</p>
          </div>
        ) : (
          <div className="space-y-3">
            {filtered.map((r) => (
              <button
                key={r.id}
                onClick={() => router.push(`/gatos/${r.gatoId}`)}
                className="w-full bg-white rounded-2xl p-4 shadow-premium border border-white text-left premium-card"
              >
                <div className="flex justify-between items-start">
                  <div>
                    <p className="font-bold text-gray-800">{r.descricao}</p>
                    <div className="flex items-center gap-2 mt-1">
                      <span className="text-xs text-gray-400">{r.gato.nome}</span>
                      <span className="text-[10px] bg-brand-50 text-brand-600 font-bold px-2 py-0.5 rounded-full capitalize">{r.tipo}</span>
                    </div>
                  </div>
                  <div className="text-right text-xs text-gray-400">
                    <p>{formatDate(r.dataEvento)}</p>
                    {r.proximaData && <p className="text-brand-600 font-bold">Próx: {formatDate(r.proximaData)}</p>}
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
