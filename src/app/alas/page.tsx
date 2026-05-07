"use client";

import { useState, useEffect } from "react";
import { useRouter } from "next/navigation";
import DashboardLayout from "@/components/DashboardLayout";

type Ala = { id: number; nome: string; descricao: string | null; totalGatos: number };

export default function AlasPage() {
  const router = useRouter();
  const [alas, setAlas] = useState<Ala[]>([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [nome, setNome] = useState("");
  const [descricao, setDescricao] = useState("");

  useEffect(() => {
    fetch("/api/alas")
      .then((r) => r.json())
      .then((json) => { if (json.success) setAlas(json.data); })
      .finally(() => setLoading(false));
  }, []);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    const res = await fetch("/api/alas", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ nome, descricao }),
    });
    const json = await res.json();
    if (json.success) {
      window.location.reload();
    }
  }

  return (
    <DashboardLayout title="Alas 🏠">
      <div className="space-y-6 -mt-10">
        <button onClick={() => setShowForm(!showForm)}
          className="bg-brand-500 text-white px-6 py-3 rounded-xl font-bold hover:bg-brand-600 transition-all shadow-lg shadow-brand-200">
          {showForm ? "Cancelar" : "+ Nova Ala"}
        </button>

        {showForm && (
          <form onSubmit={handleSubmit} className="bg-white rounded-2xl p-5 shadow-premium border border-white space-y-4">
            <input type="text" value={nome} onChange={(e) => setNome(e.target.value)} placeholder="Nome da ala" required
              className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 outline-none" />
            <textarea value={descricao} onChange={(e) => setDescricao(e.target.value)} placeholder="Descrição (opcional)" rows={2}
              className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 outline-none" />
            <button type="submit" className="w-full bg-brand-500 text-white font-bold py-3 rounded-xl hover:bg-brand-600">Criar Ala</button>
          </form>
        )}

        {loading ? (
          <div className="space-y-3">{[1, 2].map((i) => (<div key={i} className="shimmer h-16 rounded-2xl" />))}</div>
        ) : alas.length === 0 ? (
          <div className="text-center py-16"><p className="text-gray-400">Nenhuma ala cadastrada</p></div>
        ) : (
          <div className="space-y-3">
            {alas.map((ala) => (
              <div key={ala.id} className="bg-white rounded-2xl p-4 shadow-premium border border-white premium-card">
                <div className="flex justify-between items-center">
                  <div>
                    <p className="font-bold text-gray-800">{ala.nome}</p>
                    {ala.descricao && <p className="text-xs text-gray-400">{ala.descricao}</p>}
                  </div>
                  <div className="text-right">
                    <p className="text-xl font-extrabold text-brand-600">{ala.totalGatos}</p>
                    <p className="text-[10px] text-gray-400 font-bold uppercase">Gatos</p>
                  </div>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </DashboardLayout>
  );
}
