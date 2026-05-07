"use client";

import { useState, useEffect } from "react";
import DashboardLayout from "@/components/DashboardLayout";

type Adocao = {
  id: number;
  gatoId: number;
  adotanteNome: string;
  contato: string;
  dataAdocao: string;
  observacoes: string | null;
  gato: { id: number; nome: string };
};

export default function AdocoesPage() {
  const [adocoes, setAdocoes] = useState<Adocao[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetch("/api/adocoes")
      .then((r) => r.json())
      .then((json) => {
        if (json.success) setAdocoes(json.data);
      })
      .finally(() => setLoading(false));
  }, []);

  return (
    <DashboardLayout title="Adoções ❤️">
      <div className="space-y-6 -mt-10">
        <a href="/gatos" className="inline-block bg-brand-500 text-white px-6 py-3 rounded-xl font-bold hover:bg-brand-600 transition-all shadow-lg shadow-brand-200">
          + Nova Adoção (em Gatos)
        </a>

        {loading ? (
          <div className="space-y-3">{[1, 2].map((i) => (<div key={i} className="shimmer h-20 rounded-2xl" />))}</div>
        ) : adocoes.length === 0 ? (
          <div className="text-center py-16"><p className="text-gray-400">Nenhuma adoção registrada</p></div>
        ) : (
          <div className="space-y-3">
            {adocoes.map((a) => (
              <div key={a.id} className="bg-white rounded-2xl p-4 shadow-premium border border-white premium-card">
                <div className="flex justify-between items-start">
                  <div>
                    <p className="font-bold text-gray-800">{a.adotanteNome}</p>
                    <p className="text-xs text-gray-500">Gato: {a.gato.nome}</p>
                    <p className="text-xs text-gray-400">{a.contato}</p>
                    {a.observacoes && <p className="text-xs text-gray-400 mt-1">{a.observacoes}</p>}
                  </div>
                  <p className="text-xs text-gray-400">{new Date(a.dataAdocao).toLocaleDateString("pt-BR")}</p>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </DashboardLayout>
  );
}
