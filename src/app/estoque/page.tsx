"use client";

import { useState, useEffect } from "react";
import DashboardLayout from "@/components/DashboardLayout";

type EstoqueItem = {
  id: number;
  nomeItem: string;
  categoria: string;
  quantidadeAtual: number;
  unidade: string;
  estoqueMinimo: number;
  baixoEstoque: boolean;
};

export default function EstoquePage() {
  const [items, setItems] = useState<EstoqueItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [form, setForm] = useState({ nomeItem: "", categoria: "racao", quantidadeAtual: "0", unidade: "un", estoqueMinimo: "0" });

  useEffect(() => {
    fetch("/api/estoque")
      .then((r) => r.json())
      .then((json) => {
        if (json.success) setItems(json.data);
      })
      .finally(() => setLoading(false));
  }, []);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    const res = await fetch("/api/estoque", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(form),
    });
    const json = await res.json();
    if (json.success) {
      setItems((prev) => [...prev, json.data]);
      setShowForm(false);
      setForm({ nomeItem: "", categoria: "racao", quantidadeAtual: "0", unidade: "un", estoqueMinimo: "0" });
    }
  }

  const categorias: Record<string, string> = {
    racao: "Ração", areia: "Areia", medicamento: "Medicamento", limpeza: "Limpeza", outro: "Outro",
  };

  return (
    <DashboardLayout title="Estoque 📦">
      <div className="space-y-6 -mt-10">
        <button
          onClick={() => setShowForm(!showForm)}
          className="bg-brand-500 text-white px-6 py-3 rounded-xl font-bold hover:bg-brand-600 transition-all shadow-lg shadow-brand-200"
        >
          {showForm ? "Cancelar" : "+ Novo Item"}
        </button>

        {showForm && (
          <form onSubmit={handleSubmit} className="bg-white rounded-2xl p-5 shadow-premium border border-white space-y-4">
            <div className="grid grid-cols-2 gap-4">
              <div className="col-span-2">
                <input type="text" value={form.nomeItem} onChange={(e) => setForm((f) => ({ ...f, nomeItem: e.target.value }))}
                  placeholder="Nome do item" required className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 outline-none" />
              </div>
              <select value={form.categoria} onChange={(e) => setForm((f) => ({ ...f, categoria: e.target.value }))}
                className="px-4 py-3 rounded-xl border border-gray-200">
                {Object.entries(categorias).map(([k, v]) => (<option key={k} value={k}>{v}</option>))}
              </select>
              <input type="text" value={form.unidade} onChange={(e) => setForm((f) => ({ ...f, unidade: e.target.value }))}
                placeholder="Unidade" className="px-4 py-3 rounded-xl border border-gray-200" />
              <input type="number" step="0.01" value={form.quantidadeAtual} onChange={(e) => setForm((f) => ({ ...f, quantidadeAtual: e.target.value }))}
                placeholder="Quantidade" className="px-4 py-3 rounded-xl border border-gray-200" />
              <input type="number" step="0.01" value={form.estoqueMinimo} onChange={(e) => setForm((f) => ({ ...f, estoqueMinimo: e.target.value }))}
                placeholder="Estoque mínimo" className="px-4 py-3 rounded-xl border border-gray-200" />
            </div>
            <button type="submit" className="w-full bg-brand-500 text-white font-bold py-3 rounded-xl hover:bg-brand-600 transition-all">
              Adicionar
            </button>
          </form>
        )}

        {loading ? (
          <div className="space-y-3">
            {[1, 2, 3].map((i) => (<div key={i} className="shimmer h-16 rounded-2xl" />))}
          </div>
        ) : items.length === 0 ? (
          <div className="text-center py-16"><p className="text-gray-400">Estoque vazio</p></div>
        ) : (
          <div className="space-y-2">
            {items.map((item) => (
              <div key={item.id} className={`bg-white rounded-2xl p-4 shadow-premium border ${item.baixoEstoque ? "border-red-200" : "border-white"} premium-card`}>
                <div className="flex justify-between items-center">
                  <div>
                    <p className="font-bold text-gray-800">{item.nomeItem}</p>
                    <p className="text-xs text-gray-400">{categorias[item.categoria]}</p>
                  </div>
                  <div className="text-right">
                    <p className={`text-xl font-extrabold ${item.baixoEstoque ? "text-red-500" : "text-brand-600"}`}>
                      {item.quantidadeAtual}{item.unidade}
                    </p>
                    {item.baixoEstoque && <p className="text-[10px] text-red-500 font-bold">Estoque baixo!</p>}
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
