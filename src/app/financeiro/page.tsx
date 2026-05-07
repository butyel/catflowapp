"use client";

import { useState, useEffect } from "react";
import DashboardLayout from "@/components/DashboardLayout";

type FinEntry = {
  id: number;
  tipo: "receita" | "despesa";
  descricao: string;
  categoria: string;
  valor: number;
  quantidade: number;
  data: string;
  itemNome: string | null;
};

export default function FinanceiroPage() {
  const [entries, setEntries] = useState<FinEntry[]>([]);
  const [loading, setLoading] = useState(true);
  const [totalRec, setTotalRec] = useState(0);
  const [totalDesp, setTotalDesp] = useState(0);
  const [showForm, setShowForm] = useState(false);
  const [form, setForm] = useState({ tipo: "despesa", descricao: "", categoria: "outros", valor: "", data: new Date().toISOString().split("T")[0] });

  useEffect(() => {
    loadData();
  }, []);

  async function loadData() {
    setLoading(true);
    try {
      const res = await fetch("/api/financeiro");
      const json = await res.json();
      if (json.success) {
        setEntries(json.data);
        setTotalRec(json.totalReceitas);
        setTotalDesp(json.totalDespesas);
      }
    } finally {
      setLoading(false);
    }
  }

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    const res = await fetch("/api/financeiro", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(form),
    });
    const json = await res.json();
    if (json.success) {
      setShowForm(false);
      setForm({ tipo: "despesa", descricao: "", categoria: "outros", valor: "", data: new Date().toISOString().split("T")[0] });
      loadData();
    }
  }

  const catLabels: Record<string, string> = {
    racao: "Ração", veterinario: "Veterinário", areia: "Areia", medicamentos: "Medicamentos", doacao: "Doação", outros: "Outros",
  };

  const catColors: Record<string, string> = {
    racao: "bg-orange-100 text-orange-700",
    veterinario: "bg-blue-100 text-blue-700",
    areia: "bg-yellow-100 text-yellow-700",
    medicamentos: "bg-purple-100 text-purple-700",
    doacao: "bg-green-100 text-green-700",
    outros: "bg-gray-100 text-gray-700",
  };

  function formatCurrency(v: number) {
    return v.toLocaleString("pt-BR", { style: "currency", currency: "BRL" });
  }

  const saldo = totalRec - totalDesp;

  return (
    <DashboardLayout title="Financeiro 💰">
      <div className="space-y-6 -mt-10">
        {/* Summary Cards */}
        <div className="grid grid-cols-3 gap-3">
          <div className="bg-white rounded-2xl p-4 shadow-premium border border-white text-center">
            <p className="text-[10px] text-gray-400 font-bold uppercase">Entradas</p>
            <p className="text-lg font-extrabold text-green-500">{formatCurrency(totalRec)}</p>
          </div>
          <div className="bg-white rounded-2xl p-4 shadow-premium border border-white text-center">
            <p className="text-[10px] text-gray-400 font-bold uppercase">Saídas</p>
            <p className="text-lg font-extrabold text-red-500">{formatCurrency(totalDesp)}</p>
          </div>
          <div className="bg-white rounded-2xl p-4 shadow-premium border border-white text-center">
            <p className="text-[10px] text-gray-400 font-bold uppercase">Saldo</p>
            <p className={`text-lg font-extrabold ${saldo >= 0 ? "text-brand-600" : "text-red-500"}`}>{formatCurrency(saldo)}</p>
          </div>
        </div>

        <button
          onClick={() => setShowForm(!showForm)}
          className="bg-brand-500 text-white px-6 py-3 rounded-xl font-bold hover:bg-brand-600 transition-all shadow-lg shadow-brand-200"
        >
          {showForm ? "Cancelar" : "+ Novo Lançamento"}
        </button>

        {showForm && (
          <form onSubmit={handleSubmit} className="bg-white rounded-2xl p-5 shadow-premium border border-white space-y-4">
            <div className="grid grid-cols-2 gap-4">
              <div className="col-span-2">
                <input type="text" value={form.descricao} onChange={(e) => setForm((f) => ({ ...f, descricao: e.target.value }))}
                  placeholder="Descrição" required className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 outline-none" />
              </div>
              <select value={form.tipo} onChange={(e) => setForm((f) => ({ ...f, tipo: e.target.value }))}
                className="px-4 py-3 rounded-xl border border-gray-200">
                <option value="despesa">Despesa</option>
                <option value="receita">Receita</option>
              </select>
              <select value={form.categoria} onChange={(e) => setForm((f) => ({ ...f, categoria: e.target.value }))}
                className="px-4 py-3 rounded-xl border border-gray-200">
                {Object.entries(catLabels).map(([k, v]) => (<option key={k} value={k}>{v}</option>))}
              </select>
              <input type="number" step="0.01" value={form.valor} onChange={(e) => setForm((f) => ({ ...f, valor: e.target.value }))}
                placeholder="Valor" required className="px-4 py-3 rounded-xl border border-gray-200" />
              <input type="date" value={form.data} onChange={(e) => setForm((f) => ({ ...f, data: e.target.value }))}
                className="px-4 py-3 rounded-xl border border-gray-200" />
            </div>
            <button type="submit" className="w-full bg-brand-500 text-white font-bold py-3 rounded-xl hover:bg-brand-600 transition-all">
              Adicionar
            </button>
          </form>
        )}

        {loading ? (
          <div className="space-y-3">{[1, 2, 3].map((i) => (<div key={i} className="shimmer h-16 rounded-2xl" />))}</div>
        ) : entries.length === 0 ? (
          <div className="text-center py-16"><p className="text-gray-400">Nenhum lançamento</p></div>
        ) : (
          <div className="space-y-2">
            {entries.map((e) => (
              <div key={e.id} className="bg-white rounded-2xl p-4 shadow-premium border border-white premium-card">
                <div className="flex justify-between items-center">
                  <div className="flex-1">
                    <p className="font-bold text-gray-800">{e.descricao}</p>
                    <div className="flex items-center gap-2 mt-0.5">
                      <span className={`text-[10px] font-bold px-2 py-0.5 rounded-full ${catColors[e.categoria]}`}>
                        {catLabels[e.categoria]}
                      </span>
                      <span className="text-[10px] text-gray-400">{new Date(e.data).toLocaleDateString("pt-BR")}</span>
                    </div>
                  </div>
                  <p className={`text-lg font-extrabold ${e.tipo === "receita" ? "text-green-500" : "text-red-500"}`}>
                    {e.tipo === "receita" ? "+" : "-"}{formatCurrency(e.valor)}
                  </p>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </DashboardLayout>
  );
}
